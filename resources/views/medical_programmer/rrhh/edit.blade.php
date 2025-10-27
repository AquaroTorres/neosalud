@extends('layouts.app')

@section('content')
    @include('medical_programmer.nav')

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">

    <h3 class="mb-3">Editar RRHH</h3>

    <form method="POST" class="form-horizontal" action="{{ route('medical_programmer.rrhh.update', $user) }}">
        @csrf
        @method('PUT')

        <h5>Datos del usuario</h5>

        <div class="row">

            <fieldset class="form-group col-3">
                <label for="for_value">Rut</label>
                <input type="text" class="form-control" id="for_value" placeholder="Rut" name="value"
                    readonly="readonly" value="{{ $user->IdentifierRun->value }}">
            </fieldset>

            <fieldset class="form-group col-1">
                <label for="for_dv">Dv</label>
                <input type="text" class="form-control" id="for_dv" placeholder="Dv" name="dv"
                    readonly="readonly"="" value="{{ $user->IdentifierRun->dv }}">
            </fieldset>

        </div>

        <div class="row">

            <fieldset class="form-group col-4">
                <label for="for_text">Nombre</label>
                <input type="text" class="form-control" id="for_name" placeholder="" name="text" required=""
                    value="{{ $user->given }}">
            </fieldset>

            <fieldset class="form-group col-4">
                <label for="for_fathers_family">Apellido Paterno</label>
                <input type="text" class="form-control" id="for_fathers_family" placeholder="" name="fathers_family"
                    required="" value="{{ $user->fathers_family }}">
            </fieldset>

            <fieldset class="form-group col-4">
                <label for="for_mothers_family">Apellido Materno</label>
                <input type="text" class="form-control" id="for_mothers_family" placeholder="" name="mothers_family"
                    required="" value="{{ $user->mothers_family }}">
            </fieldset>
        </div>

        <hr>

        <h5>Datos adicionales</h5>

        <div class="row">

            <fieldset class="form-group col-4">
                <label for="for_job_title">Título profesional</label>
                <input type="text" class="form-control" id="for_job_title" placeholder="" name="job_title"
                    @if ($user->mpAditionals) value="{{ $user->mpAditionals->job_title }} @endif">
            </fieldset>

            <fieldset class="form-group col-4">
                <label for="for_sis_specialty">Especialidad SIS</label>
                <input type="text" class="form-control" id="for_sis_specialty" placeholder="" name="sis_specialty"
                    @if ($user->mpAditionals) value="{{ $user->mpAditionals->sis_specialty }} @endif">
            </fieldset>
        </div>

        <div class="row">

            <fieldset class="form-group col">
                <label for="risk_group">Grupo de riesgo</label>
                <select name="risk_group" id="for_risk_group" class="form-control">
                    <option value=""></option>
                    <option value="1"
                        @if ($user->mpAditionals) {{ $user->mpAditionals->risk_group == 1 ? 'selected' : '' }} @endif>
                        Sí</option>
                    <option value="0"
                        @if ($user->mpAditionals) {{ $user->mpAditionals->risk_group == 0 ? 'selected' : '' }} @endif>
                        No</option>
                </select>
            </fieldset>

            <fieldset class="form-group col">
                <label for="for_missing_condition">Ausentismo</label>
                <select name="missing_condition" id="for_missing_condition" class="form-control">
                    <option value=""></option>
                    <option value="1"
                        @if ($user->mpAditionals) {{ $user->mpAditionals->missing_condition == 1 ? 'selected' : '' }} @endif>
                        Sí</option>
                    <option value="0"
                        @if ($user->mpAditionals) {{ $user->mpAditionals->missing_condition == 0 ? 'selected' : '' }} @endif>
                        No</option>
                </select>
            </fieldset>

            <fieldset class="form-group col">
                <label for="for_missing_reason">Motivo (maternales, PSGS, Comisiones de estudio)</label>
                <input type="text" class="form-control" id="for_missing_reason"
                    name="missing_reason"@if ($user->mpAditionals) value="{{ $user->mpAditionals->missing_reason }}" @endif>
            </fieldset>

        </div>

        <!-- <div class="container">
              <div class="row">
                <div class="col-sm">
                    <h4>Permisos</h4>
                    <select class="selectpicker" name="permissions[]" multiple>
                        @foreach ($permissions as $permission)
    <option value="{{ $permission->name }}" {{ $user->hasPermissionTo($permission->name) ? 'selected' : '' }}>{{ $permission->name }}</option>
    @endforeach
                    </select>
                </div>
              </div>
            </div>

            <br />  -->
        <hr>

        <h5>Información médica</h5>

        <!-- <div class="row">
            <div class="form-group col-3">
                <h4>Especialidades</h4>
                <select id="specialties" class="selectpicker" name="specialties[]" multiple>
                    @foreach ($specialties as $specialty)
    <option value="{{ $specialty->id }}" {{ $user->specialties->contains('id', $specialty->id) ? 'selected' : '' }}>{{ $specialty->specialty_name }}</option>
    @endforeach
                </select>
            </div>

            <div class="form-group col-3">
                <h4>Especialidad Principal</h4>
                <select id="principal_specialty" name="principal_specialty">

                </select>
            </div>
          </div>

          <br>

          <div class="row">
            <div class="form-group col-3">
                <h4>Profesiones</h4>
                <select id="professions" class="selectpicker" name="professions[]" multiple>
                    @foreach ($professions as $profession)
    <option value="{{ $profession->id }}" {{ $user->professions->contains('id', $profession->id) ? 'selected' : '' }}>{{ $profession->profession_name }}</option>
    @endforeach
                </select>
            </div>

            <div class="form-group col-3">
                <h4>Profesión Principal</h4>
                <select id="principal_profession" name="principal_profession">

                </select>
            </div>
          </div> -->

        @livewire('user.user-practitioners', compact('organizations', 'professions', 'specialties', 'patient'))

        <br>

        <!-- <br />

          <div class="container">
            <div class="row">
              <div class="col-sm">
                  <h4>Box</h4>
                  <select class="selectpicker" name="operating_rooms[]" multiple>
                      @foreach ($operating_rooms as $operating_room)
    <option value="{{ $operating_room->id }}" {{ $user->userOperatingRooms->contains('operating_room_id', $operating_room->id) ? 'selected' : '' }}>{{ $operating_room->description }}</option>
    @endforeach
                  </select>
              </div>
              <div class="col-sm">

              </div>
            </div>
          </div> -->

        <button type="submit" class="btn btn-primary">Guardar</button>

    </form>

    @canany(['administrador'])
        <br />
        <hr />
        <div style="height: 300px; overflow-y: scroll;">
            @include('partials.audit', ['audits' => $user->audits])
        </div>
    @endcanany
@endsection

@section('custom_js')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}"> -->

    <!-- <script src="{{ asset('js/bootstrap-select.min.js') }}"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/defaults-es_CL.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('.selectpicker').selectpicker('refresh');

            //especialidades

            //cuando carga la hoja
            @foreach ($user->userServices as $userService)
                @if ($userService->principal == 1)
                    $('#principal_service').append('<option selected value="' + {{ $userService->service->id }} +
                        '">' + '{{ $userService->service->service_name }}' + '</option>');
                @else
                    $('#principal_service').append('<option value="' + {{ $userService->service->id }} + '">' +
                        '{{ $userService->service->service_name }}' + '</option>');
                @endif
            @endforeach

            //al modificar especialidades
            $('#services').on('change', function() {
                $('#principal_service').empty();
                $.each($("#services option:selected"), function() {
                    optionText = $(this).text();
                    optionValue = $(this).val();
                    $('#principal_service').append('<option value="' + optionValue + '">' +
                        optionText + '</option>');
                });

                //selecciona la opción principal
                @if ($user->userServices->count() > 0)
                    @foreach ($user->userServices as $userService)
                        if ({{ $userService->principal }} == 1) {
                            $("#principal_service option[value='{{ $userService->service->id }}']").attr(
                                "selected", true);
                        }
                    @endforeach
                @endif
            });


            //especialidades

            //cuando carga la hoja
            @foreach ($user->userSpecialties as $userSpecialty)
                @if ($userSpecialty->principal == 1)
                    @if ($userSpecialty->specialty)
                        $('#principal_specialty').append('<option selected value="' +
                            {{ $userSpecialty->specialty->id }} + '">' +
                            '{{ $userSpecialty->specialty->specialty_name }}' + '</option>');
                    @else
                        $('#principal_specialty').append('<option value="' + {{ $userSpecialty->specialty->id }} +
                            '">' + '{{ $userSpecialty->specialty->specialty_name }}' + '</option>');
                    @endif
                @endif
            @endforeach

            //al modificar especialidades
            $('#specialties').on('change', function() {
                $('#principal_specialty').empty();
                $.each($("#specialties option:selected"), function() {
                    optionText = $(this).text();
                    optionValue = $(this).val();
                    $('#principal_specialty').append('<option value="' + optionValue + '">' +
                        optionText + '</option>');
                });

                //selecciona la opción principal
                @if ($user->userSpecialties->count() > 0)
                    @foreach ($user->userSpecialties as $userSpecialty)
                        @if ($userSpecialty->specialty)
                            if ({{ $userSpecialty->principal }} == 1) {
                                $("#principal_specialty option[value='{{ $userSpecialty->specialty->id }}']")
                                    .attr("selected", true);
                            }
                        @endif
                    @endforeach
                @endif
            });


            //profesiones
            @foreach ($user->userProfessions as $userProfession)
                @if ($userProfession->principal == 1)
                    @if ($userProfession->profession)
                        $('#principal_profession').append('<option selected value="' +
                            {{ $userProfession->profession->id }} + '">' +
                            '{{ $userProfession->profession->profession_name }}' + '</option>');
                    @else
                        $('#principal_profession').append('<option value="' +
                            {{ $userProfession->profession->id }} + '">' +
                            '{{ $userProfession->profession->profession_name }}' + '</option>');
                    @endif
                @endif
            @endforeach

            //al modificar especialidades
            $('#professions').on('change', function() {
                $('#principal_profession').empty();
                $.each($("#professions option:selected"), function() {
                    optionText = $(this).text();
                    optionValue = $(this).val();
                    $('#principal_profession').append('<option value="' + optionValue + '">' +
                        optionText + '</option>');
                });

                //selecciona la opción principal
                @if ($user->userProfessions->count() > 0)
                    @foreach ($user->userProfessions as $userProfession)
                        @if ($userProfession->profession)
                            if ({{ $userProfession->principal }} == 1) {
                                $("#principal_profession option[value='{{ $userProfession->profession->id }}']")
                                    .attr("selected", true);
                            }
                        @endif
                    @endforeach
                @endif
            });

        });
    </script>
@endsection
