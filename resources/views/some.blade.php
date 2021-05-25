@extends('layouts.app')

@section('title', 'some')

@section('content')

<form>

	<div class="form-row mt-3">

    	<div class="form-group col-md-4">
    			<label for="inputEmail4">RUT</label>
    			<input type="email" class="form-control" id="inputEmail4" placeholder="ingrese el rut">
    	</div>
    	<div class="form-group col-md-1">
      			<label for="inputPassword4">Dv</label>
      			<input type="password" class="form-control" id="inputPassword4" placeholder="Dv">
    	</div>
		<div class="form-group col-md-5">
     	 		<label for="inputEmail4">Nombre</label>
      	 		<input type="email" class="form-control" id="inputEmail4" placeholder="Ingrese Nombre">
    	</div>
    	<div class="form-group col-md-2">
				<label for="inputEmail4">&nbsp;</label>
				<button type="button" class="btn btn-primary form-control">Buscar</button>
   		 </div>
	</div>


    <table class="table table-bordered">
         <thead class="table-secondary">
                <tr>
                    <th scope="col">Nombre:</th>
                    <th scope="col">Jose Cantero Palacios</th>
      
        </thead>
        <tbody>
                 <tr>
                     <th scope="row">Identificación</th>
                     <td>26225358-9</td>

                </tr>

                <tr>
                    <th scope="row">Edad</th>
                    <td>19 años</td>
                </tr>

                <tr>
                     <th scope="row">Sexo</th>
                     <td colspan="2">Masculino</td>
                </tr>

                <tr>
                     <th scope="row">Dirección</th>
                     <td colspan="2">Anibal pinto 814</td>
                </tr>

                <tr>
                      <th scope="row">Teléfono</th>
                      <td colspan="2">942422656</td>
                </tr>

                <tr>
                    <th scope="row">Correo</th>
                    <td colspan="2">josecp@gmail.com</td>
                </tr>
        </tbody>
    </table>


	<div class="card mb-3">
		<div class="card-body">
		
			<div class="form-row">

    			<div class="form-group col-md-4">
    				<p class="card-text">Prevision</p>
    			</div>
				<div class="form-group col-md-6">
    				<p class="card-text">Fonasa</p>
    			</div>

    			<div class="form-group col-md-2">
					<button type="button" class="btn btn-primary form-control">Fonasa</button>
   			    </div>
			</div>
		</div>
	</div>


	<div class="form-row">

    	<div class="form-group col-md-4">
    			<label for="inputEmail4">Especialidad</label>
    			<select id="inputState" class="form-control">
        			<option selected>Salud Mental</option>
        			<option>Traumatologia</option>
     			 </select>
    	</div>
    	<div class="form-group col-md-4">
      			<label for="inputPassword4">Profesional</label>
      			<select id="inputState" class="form-control">
        			<option selected>Dr Oscar Zavala</option>
        			<option>Dr Toby Cerdo</option>
     			 </select>
    	</div>
		<div class="form-group col-md-4">
     	 		<label for="inputEmail4">Estado</label>
				  <select id="inputState" class="form-control">
        			<option selected>Disponible</option>
        			<option>Bloqueado</option>
     			 </select>
    	</div>
    	
	</div>

	
	<div class="form-group">
    	<div class="form-check">
      		<input class="form-check-input" type="checkbox" id="gridCheck">
      		<label class="form-check-label" for="gridCheck">
      			  Proxima Fecha Disponible
      	    </label>
    	</div>
  	</div>



	<div class="form-row">

		<div class="form-group col-md-5">
			<label for="inputEmail4">Desde</label>
			<input type="date" class="form-control" id="inputEmail4" placeholder="Fecha inicio">
		</div>

		<div class="form-group col-md-5">
		  	<label for="inputEmail4">Hasta</label>
		   	<input type="date" class="form-control" id="inputEmail4" placeholder="Fecha fin">
		</div>
		<div class="form-group col-md-2">
			<label for="inputEmail4">&nbsp;</label>
			<button type="button" class="btn btn-primary form-control">Buscar</button>
		</div>
	</div>

	<table class="table table-striped">
  		<thead>
    		<tr>
      			<th scope="col">Profesional</th>
      			<th scope="col">Hora</th>
      			<th scope="col">Cupo</th>
      			<th scope="col">Sobre Cupo</th>
	  			<th scope="col">Estado</th>
    		</tr>
  		</thead>
  		<tbody>
   			<tr>
     			<th scope="row">Esteban Rojas</th>
      			<td>10:30</td>
      			<td>3</td>
      			<td>2</td>
				<td>Disponible</td>
    		</tr>

			<tr>
     			<th scope="row">Maria Perez</th>
      			<td>11:30</td>
      			<td>2</td>
      			<td>0</td>
				<td>Disponible</td>
    		</tr>

			<tr>
     			<th scope="row">Juan Zavala</th>
      			<td>8:00</td>
      			<td>1</td>
      			<td>0</td>
				<td>Disponible</td>
    		</tr>
    
 		 </tbody>
	</table>



</form>


@endsection

@section('custom_js')

@endsection