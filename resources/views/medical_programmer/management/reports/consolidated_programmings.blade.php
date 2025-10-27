@extends('layouts.app')

@section('content')
    @include('medical_programmer.nav')

    <h3 class="mb-3">Consolidado de programaciones</h3>

    <form method="GET" id="form" class="form-horizontal"
        action="{{ route('medical_programmer.programming_proposal.consolidated_programmings') }}">

        <form>
            <div class="row">
                <div class="col-3">
                    <label for="for_date">Fecha</label>
                    <input type="week" name="date" class="form-control" value="{{ $request->date }}">
                </div>
                <div class="col-3">
                    <label for="for_specialty_id">Especialidades</label>
                    <select name="specialty_id" id="" class="form-control">
                        <option value=""></option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected($request->specialty_id == $specialty->id)>
                                {{ $specialty->specialty_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <label for="for_profession_id">Profesiones</label>
                    <select name="profession_id" id="" class="form-control">
                        <option value=""></option>
                        @foreach ($professions as $profession)
                            <option value="{{ $profession->id }}" @selected($request->profession_id == $profession->id)>
                                {{ $profession->profession_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <label for="for_button"></label>
                    <button type="submit" class="btn btn-primary form-control">Buscar</button>
                </div>
            </div>
        </form>

    </form>

    <hr>

    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button"
                role="tab" aria-controls="nav-home" aria-selected="true">Médico</button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button"
                role="tab" aria-controls="nav-profile" aria-selected="false">No médico</button>
        </div>
    </nav>

    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">

            <table class="table table-sm table-bordered text-center table-striped small">
                <thead>
                    <tr class="text-center">
                        <th>Nombre</th>
                        <th>Contrato</th>
                        <th>Especialidad</th>
                        <th>Actividad</th>
                        <th>Hrs. Asignadas</th>
                        <th>Rdto/Hr</th>
                        <th>Días Laborales Efectivos</th>
                        <th>Semanas Laborales Efectivas</th>
                        <th>Rdto/Mensual</th>
                        <th>Rdto/Anual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($array_medic_programmings as $key1 => $array_medic_programming)
                        @foreach ($array_medic_programming as $key2 => $contracts)
                            @foreach ($contracts as $key3 => $specialties)
                                @foreach ($specialties as $key4 => $activities)
                                    <tr>
                                        <td>{{ $key1 }}</td>
                                        <td>{{ $key2 }}</td>
                                        <td>{{ $key3 }}</td>
                                        <td>{{ $key4 }}</td>
                                        <td>{{ $activities['hours'] }}</td>
                                        <td>{{ $activities['performance'] }}</td>
                                        <td>{{ $activities['effective_worked_days'] }}</td>
                                        <td>{{ round($activities['effective_worked_days'] / 5) }}</td>
                                        @if ($activities['performance'] != null)
                                            <td>{{ round((round($activities['effective_worked_days'] / 5) * $activities['hours'] * $activities['performance']) / 12) }}
                                            </td>
                                            <td>{{ round(round($activities['effective_worked_days'] / 5) * $activities['hours'] * $activities['performance']) }}
                                            </td>
                                        @else
                                            <td></td>
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>

        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">

            <table class="table table-sm table-bordered text-center table-striped small">
                <thead>
                    <tr class="text-center">
                        <th>Rut</th>
                        <th>Contrato</th>
                        <th>Especialidad</th>
                        <th>Actividad</th>
                        <th>Hrs. Asignadas</th>
                        <th>Rdto/Hr</th>
                        <th>Días Laborales Efectivos</th>
                        <th>Semanas Laborales Efectivas</th>
                        <th>Rdto/Mensual</th>
                        <th>Rdto/Anual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($array_non_medic_programmings as $key1 => $array_medic_programming)
                        @foreach ($array_medic_programming as $key2 => $contracts)
                            @foreach ($contracts as $key3 => $specialties)
                                @foreach ($specialties as $key4 => $activities)
                                    <tr>
                                        <td>{{ $key1 }}</td>
                                        <td>{{ $key2 }}</td>
                                        <td>{{ $key3 }}</td>
                                        <td>{{ $key4 }}</td>
                                        <td>{{ $activities['hours'] }}</td>
                                        <td>{{ $activities['performance'] }}</td>
                                        <td>{{ $activities['effective_worked_days'] }}</td>
                                        <td>{{ round($activities['effective_worked_days'] / 5) }}</td>
                                        @if ($activities['performance'] != null)
                                            <td>{{ round((round($activities['effective_worked_days'] / 5) * $activities['hours'] * $activities['performance']) / 12) }}
                                            </td>
                                            <td>{{ round(round($activities['effective_worked_days'] / 5) * $activities['hours'] * $activities['performance']) }}
                                            </td>
                                        @else
                                            <td></td>
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('custom_js')
@endsection
