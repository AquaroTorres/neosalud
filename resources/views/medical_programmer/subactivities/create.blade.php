@extends('layouts.app')

@section('content')

<link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css"/>

<h3 class="mb-3">Nueva Subactividad</h3>

<form method="POST" class="form-horizontal" action="{{ route('medical_programmer.subactivities.store') }}">
  @csrf
  @method('POST')

    @livewire('medical_programmer.specialty-activities-selection',compact('specialties','activities'))

    <div class="row">
        <fieldset class="form-group col-3">
          <label for="for_sub_activity_abbreviated">Abreviado</label>
          <input type="text" class="form-control" id="for_sub_activity_abbreviated" placeholder="" name="sub_activity_abbreviated" required>
        </fieldset>

        <fieldset class="form-group col-3">
          <label for="for_sub_activity_name">Nombre</label>
          <input type="text" class="form-control" id="for_sub_activity_name" placeholder="" name="sub_activity_name" required>
        </fieldset>

        <fieldset class="form-group col-4">
          <label for="for_sub_activity_description">Descripción</label>
          <input type="text" class="form-control" id="for_sub_activity_description" placeholder="" name="sub_activity_description">
        </fieldset>

        <fieldset class="form-group col">
          <label for="for_performance">Rendimiento</label>
          <input type="text" class="form-control" id="for_performance" placeholder="" name="performance" required>
        </fieldset>
    </div>

  <button type="submit" class="btn btn-primary">Guardar</button>

</form>

@endsection

@section('custom_js')

<script src="{{ asset('js/bootstrap-select.min.js') }}"></script>

@endsection
