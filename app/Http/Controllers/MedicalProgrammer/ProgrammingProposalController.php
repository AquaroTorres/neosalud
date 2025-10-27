<?php

namespace App\Http\Controllers\MedicalProgrammer;

use App\Http\Controllers\Controller;
use App\Models\MedicalProgrammer\Profession;
use App\Models\MedicalProgrammer\ProgrammingProposal;
use App\Models\MedicalProgrammer\ProgrammingProposalDetail;
use App\Models\MedicalProgrammer\ProgrammingProposalSignatureFlow;
use App\Models\MedicalProgrammer\Specialty;
use App\Models\MedicalProgrammer\UnitHead;
use App\Models\User;
use Carbon\Carbon;
// use App\Models\MedicalProgrammer\TheoreticalProgramming;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgrammingProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name                 = $request->get('name');
        $programmingProposals = [];
        if (Auth::user()->hasPermissionTo('Mp: perfil administrador')) {
            $programmingProposals = ProgrammingProposal::orderBy('id', 'DESC')
                ->when($name != null, function ($q) use ($name) {
                    return $q->whereHas('user', function ($query) use ($name) {
                        return $query->whereHas('humanNames', function ($query) use ($name) {
                            return $query->where('text', 'LIKE', '%'.$name.'%')
                                ->orwhere('fathers_family', 'LIKE', '%'.$name.'%')
                                ->orwhere('mothers_family', 'LKE', '%'.$name.'%');
                        });
                    });
                })
                ->whereHas('user', function ($query) {
                    return $query->whereHas('practitioners', function ($q) {
                        return $q->whereIn('organization_id', auth()->user()->practitionersOrganizations());
                    });
                })
                ->paginate(50);
        } else {
            // cuando el usuario está asignado como jefe de unidad
            if (Auth::user()->hasPermissionTo('Mp: perfil jefe de unidad')) {
                $unitHeads_specialty  = UnitHead::where('user_id', Auth::id())->pluck('specialty_id');
                $unitHeads_profession = UnitHead::where('user_id', Auth::id())->pluck('profession_id');

                $programmingProposals = ProgrammingProposal::orderBy('id', 'DESC')
                    ->when($name != null, function ($q) use ($name) {
                        return $q->whereHas('user', function ($query) use ($name) {
                            return $query->whereHas('humanNames', function ($query) use ($name) {
                                return $query->where('text', 'LIKE', '%'.$name.'%')
                                    ->orwhere('fathers_family', 'LIKE', '%'.$name.'%')
                                    ->orwhere('mothers_family', 'LKE', '%'.$name.'%');
                            });
                        });
                    })
                    ->whereHas('user', function ($query) {
                        return $query->whereHas('practitioners', function ($q) {
                            return $q->whereIn('organization_id', auth()->user()->practitionersOrganizations());
                        });
                    })
                    ->whereIn('specialty_id', $unitHeads_specialty)
                    ->OrwhereIn('profession_id', $unitHeads_profession)
                    ->paginate(50);
            }
            // cuando el usuario está asignado como visador
            elseif (1) {

            } else {
                $programmingProposals = ProgrammingProposal::where('id', 0)->paginate(50);
            }
        }

        return view('medical_programmer.programming_proposals.index', compact('request', 'programmingProposals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('medical_programmer.programming_proposals.create', compact('request'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $programmingProposal               = new ProgrammingProposal($request->All());
        $programmingProposal->type         = $request->proposal_type;
        $programmingProposal->request_date = Carbon::now();
        $programmingProposal->status       = 'Creado';
        $programmingProposal->save();

        $programmingProposalSignatureFlow                          = new ProgrammingProposalSignatureFlow;
        $programmingProposalSignatureFlow->programming_proposal_id = $programmingProposal->id;
        $programmingProposalSignatureFlow->user_id                 = Auth::id();
        $programmingProposalSignatureFlow->sign_position           = 1;
        $programmingProposalSignatureFlow->signature_date          = Carbon::now();
        $programmingProposalSignatureFlow->status                  = 'Solicitud creada';

        if (Auth::user()->unitHead->count() > 0) {
            $programmingProposalSignatureFlow->type = 'Jefe de Servicio';
        }

        if (Auth::user()->programmerVisator->count() > 0) {
            $programmingProposalSignatureFlow->type = Auth::user()->programmerVisator->first()->permission;
        }

        $programmingProposalSignatureFlow->save();

        session()->flash('success', 'Se ha registrado la información.');

        return redirect()->route('medical_programmer.programming_proposal.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_confirmation(Request $request, ProgrammingProposal $programmingProposal)
    {
        $last_position = $programmingProposal->signatureFlows->last()->sign_position;

        $programmingProposalSignatureFlow                          = new ProgrammingProposalSignatureFlow;
        $programmingProposalSignatureFlow->programming_proposal_id = $programmingProposal->id;
        $programmingProposalSignatureFlow->user_id                 = Auth::id();
        $programmingProposalSignatureFlow->sign_position           = $last_position + 1;
        $programmingProposalSignatureFlow->signature_date          = Carbon::now();

        if ($request->buttonaction == 'accept_button') {
            $programmingProposalSignatureFlow->status = 'Solicitud confirmada';

            if (Auth::user()->programmerVisator->count() > 0) {
                $programmingProposal->status = 'Confirmado';
                $programmingProposal->save();
            } else {
                $programmingProposal->status = 'En proceso';
                $programmingProposal->save();
            }
        } else {
            $programmingProposalSignatureFlow->status = 'Solicitud rechazada';

            $programmingProposal->status = 'Rechazado';
            $programmingProposal->save();
        }

        if (Auth::user()->unitHead->count() > 0) {
            $programmingProposalSignatureFlow->type = 'Jefe de Servicio';
        }

        if (Auth::user()->programmerVisator->count() > 0) {
            $programmingProposalSignatureFlow->type = Auth::user()->programmerVisator->first()->permission;
        }

        $programmingProposalSignatureFlow->observation = $request->observation;
        $programmingProposalSignatureFlow->save();

        session()->flash('success', 'Se ha registrado la información.');

        return redirect()->route('medical_programmer.programming_proposal.edit', $programmingProposal);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ProgrammingProposal $programmingProposal)
    {
        // obtiene detalles ingresados para mostrarlos en jcalendar
        $start_date      = $programmingProposal->start_date;
        $end_date        = $programmingProposal->end_date;
        $programmed_days = [];
        $count           = 0;

        while ($start_date <= $end_date) {
            $dayOfWeek = $start_date->dayOfWeek;

            foreach ($programmingProposal->details->where('day', $dayOfWeek) as $key => $detail) {
                $programmed_days[$count]['start_date'] = $start_date->format('Y-m-d').' '.$detail->start_hour->format('H:s');
                $programmed_days[$count]['end_date']   = $start_date->format('Y-m-d').' '.$detail->end_hour->format('H:s');
                $programmed_days[$count]['data']       = $detail;
                $count += 1;
            }
            $start_date->addDays(1);
        }

        $total_hours = 0;
        foreach ($programmingProposal->details as $key => $detail) {
            $total_hours += Carbon::parse($detail->end_hour->format('H:s'))->diffInMinutes(Carbon::parse($detail->start_hour->format('H:s'))) / 60;
        }

        // obtiene teoricos para mostrar en jcalendar

        // $theoreticalProgrammings = TheoreticalProgramming::where('user_id',$programmingProposal->user_id)
        //                                                  ->where('contract_id',$programmingProposal->contract_id)
        //                                                  ->where('specialty_id',$programmingProposal->specialty_id)
        //                                                  ->whereBetween('start_date',[$programmingProposal->start_date,$programmingProposal->end_date])
        //                                                  ->get();

        // obtiene posibles programaciones anteriores para comparación
        $last_programmingProposal = ProgrammingProposal::where('user_id', $programmingProposal->user_id)
            ->where('contract_id', $programmingProposal->contract_id)
            ->where('specialty_id', $programmingProposal->specialty_id)
            ->where('id', '<', $programmingProposal->id)
            ->where('status', 'Confirmado')
            ->latest()->first();

        $last_programmed_days = [];
        if ($last_programmingProposal != null) {
            $start_date = $last_programmingProposal->start_date;
            $end_date   = $last_programmingProposal->end_date;
            $count      = 0;

            while ($start_date <= $end_date) {
                $dayOfWeek = $start_date->dayOfWeek;

                foreach ($last_programmingProposal->details->where('day', $dayOfWeek) as $key => $detail) {
                    $last_programmed_days[$count]['start_date'] = $start_date->format('Y-m-d').' '.$detail->start_hour->format('H:s');
                    $last_programmed_days[$count]['end_date']   = $start_date->format('Y-m-d').' '.$detail->end_hour->format('H:s');
                    $last_programmed_days[$count]['data']       = $detail;
                    $count += 1;
                }
                $start_date->addDays(1);
            }
        }

        // otras fichas de la persona
        $other_contracts_info = [];
        foreach ($programmingProposal->user->programmingProposals->where('status', '!=', 'Rechazado')->where('id', '!=', $programmingProposal->id) as $key => $programmingProposal_) {
            if ($programmingProposal_ != null) {
                $start_date = $programmingProposal_->start_date;
                $end_date   = $programmingProposal_->end_date;
                $count      = 0;

                while ($start_date <= $end_date) {
                    $dayOfWeek = $start_date->dayOfWeek;

                    foreach ($programmingProposal_->details->where('day', $dayOfWeek) as $key => $detail) {
                        $other_contracts_info[$count]['start_date'] = $start_date->format('Y-m-d').' '.$detail->start_hour;
                        $other_contracts_info[$count]['end_date']   = $start_date->format('Y-m-d').' '.$detail->end_hour;
                        $other_contracts_info[$count]['data']       = $detail;
                        $count += 1;
                    }
                    $start_date->addDays(1);
                }
            }
        }
        // dd($other_contracts_info);

        return view('medical_programmer.programming_proposals.edit', compact('programmingProposal', 'last_programmed_days',
            'programmed_days', 'total_hours', 'other_contracts_info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProgrammingProposal $programmingProposal)
    {
        $programmingProposal->delete();
        session()->flash('success', 'Se ha eliminado la información.');

        return redirect()->route('medical_programmer.programming_proposal.index');
    }

    public function programming_by_practioner(Request $request)
    {
        $user_id              = $request->get('user_id');
        $user                 = User::find($user_id);
        $programmingProposals = ProgrammingProposal::where('id', 0)->get();
        if ($user != null) {

            $now                  = Carbon::now();
            $programmingProposals = ProgrammingProposal::where('user_id', $request->user_id)
                                                      // ->where('contract_id',$programmingProposal->contract_id)
                ->where('specialty_id', $request->specialty_id)
                ->where('status', 'Confirmado')
                ->whereYear('request_date', $now->format('Y'))
                ->get();

            foreach ($programmingProposals as $key => $programmingProposal) {
                $array = [];
                foreach ($programmingProposal->details as $key => $detail) {
                    if ($detail->subactivity) {
                        $array[$detail->activity->activity_name.' - '.$detail->subactivity->sub_activity_name] = 0;
                    } else {
                        $array[$detail->activity->activity_name] = 0;
                    }
                }
                foreach ($programmingProposal->details as $key => $detail) {
                    if ($detail->subactivity) {
                        $array[$detail->activity->activity_name.' - '.$detail->subactivity->sub_activity_name] += Carbon::parse($detail->end_hour)->diffInMinutes(Carbon::parse($detail->start_hour->format('H:s'))) / 60;
                    } else {
                        $array[$detail->activity->activity_name] += Carbon::parse($detail->end_hour)->diffInMinutes(Carbon::parse($detail->start_hour->format('H:s'))) / 60;
                    }
                }
                $programmingProposal->array = $array;
            }

            // dd($programmingProposals->first()->details);
        }

        return view('some.programming_by_practioner', compact('request', 'programmingProposals'));
    }

    public function consolidated_programmings(Request $request)
    {

        $theoreticalProgrammings      = null;
        $programmingProposals         = null;
        $programmed_days              = [];
        $array_medic_programmings     = [];
        $array_non_medic_programmings = [];

        $request_start_date = CarbonImmutable::parse($request->date);
        $request_end_date   = $request_start_date->addDays(6);

        if ($request->date != null) {
            $programmingProposals = ProgrammingProposal::with('user', 'contract', 'specialty', 'profession', 'details.activity', 'profession')
                ->when($request->specialty_id != null && $request->profession_id == null, function ($query) use ($request) {
                    $query->where('specialty_id', $request->specialty_id);
                })
                ->when($request->profession_id != null && $request->specialty_id == null, function ($query) use ($request) {
                    $query->where('profession_id', $request->profession_id);
                })
                ->when($request->profession_id != null && $request->specialty_id != null, function ($query) use ($request) {
                    $query->where('specialty_id', $request->specialty_id)
                        ->orWhere('profession_id', $request->profession_id);
                })
                ->whereYear('start_date', $request_start_date->format('Y'))
                ->orderBy('user_id')
                ->orderBy('request_date', 'ASC') // debe ir así, para que deje los primeros ingresados al principio, y aspi se puedan ordenar correctamente y no se pisen
                ->get();

            // dd($programmingProposals);

            $programmed_days = [];
            $count           = 0;
            // ciclo para obtener fechas
            foreach ($programmingProposals as $key => $programmingProposal) {

                // dd($programmingProposal);
                $start_date = clone $request_start_date;
                $end_date   = clone $request_end_date;

                // dd($programmingProposal->contract);
                $programmingProposal->aux_OfficialFullName  = $programmingProposal->user->OfficialFullName;
                $programmingProposal->aux_contract_id       = $programmingProposal->contract->contract_id;
                $programmingProposal->effective_worked_days = 247 - ($programmingProposal->contract->legal_holidays + $programmingProposal->contract->compensatory_rest + $programmingProposal->contract->administrative_permit + $programmingProposal->contract->training_days + // $programmingProposal->contract->breastfeeding_time +
                                                                    $programmingProposal->contract->covid_permit
                );
                if ($programmingProposal->specialty) {
                    $programmingProposal->aux_id_specialty   = $programmingProposal->specialty->id_specialty;
                    $programmingProposal->aux_specialty_name = $programmingProposal->specialty->specialty_name;
                }
                if ($programmingProposal->profession) {
                    $programmingProposal->aux_id_profession   = $programmingProposal->profession->id_profession;
                    $programmingProposal->aux_profession_name = $programmingProposal->profession->profession_name;
                }

                // dd($programmingProposal);

                // se obtienen los del periodo actual
                foreach ($programmingProposal->details as $key2 => $detail) {

                    // solo los que esten en el rango de fechas
                    if (Carbon::parse($start_date->format('Y-m-d'))->between($request_start_date, $request_end_date)) {
                        $programmed_days[$programmingProposal->user_id][$count]['duration']              = $detail->start_hour->diffInMinutes($detail->end_hour) / 60;
                        $programmed_days[$programmingProposal->user_id][$count]['data']                  = $detail;
                        $programmed_days[$programmingProposal->user_id][$count]['fullName']              = $programmingProposal->aux_OfficialFullName;
                        $programmed_days[$programmingProposal->user_id][$count]['contractId']            = $programmingProposal->aux_contract_id;
                        $programmed_days[$programmingProposal->user_id][$count]['effective_worked_days'] = $programmingProposal->effective_worked_days;
                        $programmed_days[$programmingProposal->user_id][$count]['activity']              = $detail->activity?->id_activity.' - '.$detail->activity?->activity_name;
                        if ($programmingProposal->aux_id_specialty) {
                            $programmed_days[$programmingProposal->user_id][$count]['specialty'] = $programmingProposal->aux_id_specialty.' - '.$programmingProposal->aux_specialty_name;
                        } else {
                            $programmed_days[$programmingProposal->user_id][$count]['specialty'] = '';
                        }
                        if ($programmingProposal->aux_id_profession) {
                            $programmed_days[$programmingProposal->user_id][$count]['profession'] = $programmingProposal->aux_id_profession.' - '.$programmingProposal->aux_profession_name;
                        } else {
                            $programmed_days[$programmingProposal->user_id][$count]['profession'] = '';
                        }

                        $count += 1;
                    }
                }
            }

            // dd($programmed_days);

            // se obtiene array final
            foreach ($programmed_days as $key => $programmed_day) {

                foreach ($programmed_day as $key2 => $value) {
                    if ($value['specialty'] != '') {
                        $specialty                                                                                                                            = $value['specialty'];
                        $array_medic_programmings[$value['fullName']][$value['contractId']][$value['specialty']][$value['activity']]['hours']                 = 0;
                        $array_medic_programmings[$value['fullName']][$value['contractId']][$value['specialty']][$value['activity']]['performance']           = 0;
                        $array_medic_programmings[$value['fullName']][$value['contractId']][$value['specialty']][$value['activity']]['effective_worked_days'] = 0;
                    }
                    // profesiones
                    if ($value['data']->programmingProposal->profession) {
                        $profesion                                                                                                                                 = $value['profession'];
                        $array_non_medic_programmings[$value['fullName']][$value['contractId']][$value['profession']][$value['activity']]['hours']                 = 0;
                        $array_non_medic_programmings[$value['fullName']][$value['contractId']][$value['profession']][$value['activity']]['performance']           = 0;
                        $array_non_medic_programmings[$value['fullName']][$value['contractId']][$value['profession']][$value['activity']]['effective_worked_days'] = 0;
                    }
                }

                foreach ($programmed_day as $key => $value) {
                    // especialidades
                    if ($value['specialty'] != '') {
                        $array_medic_programmings[$value['fullName']][$value['contractId']][$value['specialty']][$value['activity']]['hours'] += $value['duration']; // $start->diffInMinutes($end)/60;
                        $array_medic_programmings[$value['fullName']][$value['contractId']][$value['specialty']][$value['activity']]['performance']           = $value['data']->activity?->specialties->where('id', $value['data']->programmingProposal->specialty_id)->first()->pivot->performance;
                        $array_medic_programmings[$value['fullName']][$value['contractId']][$value['specialty']][$value['activity']]['effective_worked_days'] = $value['effective_worked_days'];
                    }
                    // profesiones
                    if ($value['data']->programmingProposal->profession) {
                        $array_non_medic_programmings[$value['fullName']][$value['contractId']][$value['profession']][$value['activity']]['hours'] += $value['duration']; // $start->diffInMinutes($end)/60;
                        $array_non_medic_programmings[$value['fullName']][$value['contractId']][$value['profession']][$value['activity']]['performance'] = $value['data']->activity->professions->where('id', $value['data']->programmingProposal->profession_id)->first()->pivot->performance;
                        $array_non_medic_programmings[$value['fullName']][$value['contractId']][$value['profession']][$value['activity']]['effective_worked_days'] += $value['effective_worked_days']; // $start->diffInMinutes($end)/60;
                    }
                }
            }

            // dd($array_medic_programmings, $array_non_medic_programmings);
        }

        $specialties = Specialty::all();
        $professions = Profession::all();

        return view('medical_programmer.management.reports.consolidated_programmings', compact('array_medic_programmings',
            'array_non_medic_programmings',
            'specialties',
            'professions',
            'request'));
    }
}
