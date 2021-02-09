<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\User;

class ClaveUnicaController extends Controller
{
    public function autenticar(Request $request){
        /* Primer paso, redireccionar al login de clave única */
        $url_base = "https://accounts.claveunica.gob.cl/accounts/login/?next=/openid/authorize";
        $client_id = env("CLAVEUNICA_CLIENT_ID");
        $redirect_uri = urlencode(env("CLAVEUNICA_CALLBACK"));
        $state = csrf_token();
        $scope = 'openid+run+name+email';

        $url=$url_base.urlencode('?client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&scope='.$scope.'&response_type=code&state='.$state);
        return redirect()->to($url)->send();
    }

    public function callback(Request $request) {
        /* Segundo Paso: enviar credenciales de clave única */
        $code   = $request->input('code');
        $state  = $request->input('state');
        //die('Regresó a callback');
        //$state = csrf_token(); /* TODO:  Validar que el state sea el mismo que viene de clave única */

        $url_base       = "https://accounts.claveunica.gob.cl/openid/token/";
        $client_id      = env("CLAVEUNICA_CLIENT_ID");
        $client_secret  = env("CLAVEUNICA_SECRET_ID");
        $redirect_uri   = urlencode(env("CLAVEUNICA_CALLBACK"));
        $scope          = 'openid+run+name+email';

        $response = Http::asForm()->post($url_base, [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'state' => $state,
        ]);

        dd(json_decode($response));
        /* Tercer Paso, obtener los datos de usuario  */
        $access_token = json_decode($response)->access_token;
        $url_base = "https://www.claveunica.gob.cl/openid/userinfo";
        $response = Http::withToken($access_token)->get($url_base);

        $user_clave_unica = json_decode($response);

        $user_local = User::find($user_clave_unica->RolUnico->numero);

        if($user_local) {
            /* Actualiza el correo si es que ha cambiado */
            if($user_local->email != $user_clave_unica->email) {
                $user_local->email = $user_clave_unica->email;
                $user_local->save();
            }
        } 
        else {
            $user_local = new User();
            $user_local->id = $user_clave_unica->RolUnico->numero;
            $user_local->dv = $user_clave_unica->RolUnico->DV;
            $user_local->name = implode(' ', $user_clave_unica->name->nombres);
            $user_local->fathers_family = $user_clave_unica->name->apellidos[0];
            $user_local->mothers_family = $user_clave_unica->name->apellidos[1];
            $user_local->email = $user_clave_unica->email;
            $user_local->save();
        }

        Auth::login($user_local, true);
        
        return redirect()->route('home');
        
        /*
        [RolUnico] => stdClass Object
            (
                [DV] => 4
                [numero] => 44444444
                [tipo] => RUN
            )

        [sub] => 2594
        [name] => stdClass Object
            (
                [apellidos] => Array
                    (
                        [0] => Del rio
                        [1] => Gonzalez
                    )

                [nombres] => Array
                    (
                        [0] => Maria
                        [1] => Carmen
                        [2] => De los angeles
                    )

            )
        [email] => mcdla@mail.com
        */
    }

    public function login($access_token = null)
    {
        if ($access_token) {
            //dd($access_token);
            if (env('APP_ENV') == 'production') {
                //$access_token = session()->get('access_token');
                $url_base = "https://www.claveunica.gob.cl/openid/userinfo";
                $response = Http::withToken($access_token)->get($url_base);

                $user_cu = json_decode($response);
                $user = new User();
                $user->id = $user_cu->RolUnico->numero;
                $user->dv = $user_cu->RolUnico->DV;
                $user->name = implode(' ', $user_cu->name->nombres);
                $user->fathers_family = $user_cu->name->apellidos[0];
                $user->mothers_family = $user_cu->name->apellidos[1];
                $user->email = $user_cu->email;
            } elseif (env('APP_ENV') == 'local') {
                $user = new User();
                $user->id = 12345678;
                $user->dv = 9;
                $user->name = "Administrador";
                $user->fathers_family = "Ap1";
                $user->mothers_family = "Ap2";
                $user->email = "email@email.com";
            }

            $u = User::find($user->id);
            if($u) {
                $u->name = $user->name;
                $u->fathers_family = $user->fathers_family;
                $u->mothers_family = $user->mothers_family;
                $u->email_personal = $user->email;
                $u->save();
                Auth::login($u, true);
                $route = 'home';
            }
            else {
                session()->flash('danger', 'No existe el usuario registrado en el sistema');
                $route = 'login';
            }

            return redirect()->route($route);
            //Auth::loginUsingId($user->id, true);
        }
    }
}
