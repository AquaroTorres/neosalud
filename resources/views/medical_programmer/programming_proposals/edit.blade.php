@extends('layouts.app')

@section('content')
    @include('medical_programmer.nav')

    <h3 class="mb-3">Editar ficha de programación</h3>

    <div class="row">
        <fieldset class="form-group col col-md-4">
            <label for="for_id_deis">Tipo</label>
            <input type="text" class="form-control" name="" value="{{ $programmingProposal->type }}" disabled>
        </fieldset>
        <fieldset class="form-group col col-md">
            <label for="for_id_deis">Fecha Inicio</label>
            <input type="text" class="form-control" name=""
                value="{{ $programmingProposal->start_date->format('d-m-Y') }}" disabled>
        </fieldset>

        <fieldset class="form-group col col-md">
            <label for="for_id_deis">Fecha Término</label>
            <input type="text" class="form-control" name=""
                value="{{ $programmingProposal->end_date->format('d-m-Y') }}" disabled>
        </fieldset>
    </div>

    <div class="row">

        <fieldset class="form-group col col-md">
            <label for="for_id_deis">Trabajador</label>
            <input type="text" class="form-control" name=""
                value="{{ $programmingProposal->user->OfficialFullName }}" disabled>
        </fieldset>

        <fieldset class="form-group col col-md">
            <label for="for_id_deis">Contrato</label>
            <input type="text" class="form-control" name=""
                @if ($programmingProposal->contract) value="{{ $programmingProposal->contract->law }} - {{ $programmingProposal->contract->weekly_hours }}hrs - {{ $programmingProposal->contract->year }}" @endif
                disabled>
        </fieldset>

        @if ($programmingProposal->specialty)
            <fieldset class="form-group col col-md">
                <label for="for_id_deis">Especialidad</label>
                <input type="text" class="form-control" name=""
                    value="{{ $programmingProposal->specialty->specialty_name }}" disabled>
            </fieldset>
        @endif

        @if ($programmingProposal->profession)
            <fieldset class="form-group col col-md">
                <label for="for_id_deis">Especialidad</label>
                <input type="text" class="form-control" name=""
                    value="{{ $programmingProposal->profession->profession_name }}" disabled>
            </fieldset>
        @endif


    </div>

    <div class="row">
        <fieldset class="form-group col col-md">
            <label for="for_id_deis">Observación</label>
            <textarea name="name" class="form-control" rows="3" cols="80" disabled>{{ $programmingProposal->observation }}</textarea>
        </fieldset>
    </div>

    <!-- <button type="submit" class="btn btn-primary">Crear</button> -->

    <hr>

    <h4>Detalles</h4>

    @if ($programmingProposal->employeeCanModify($programmingProposal) == 1)
        <a class="btn btn-primary mb-3"
            href="{{ route('medical_programmer.programming_proposal.details.create', $programmingProposal) }}">
            <i class="fas fa-plus"></i> Agregar detalle
        </a>
    @else
        <button type="submit" class="btn btn-primary" disabled><i class="fas fa-plus"></i>
            Agregar detalle
        </button>
        <br><br>
    @endif

    <table class="table table-sm table-borderer">
        <thead>
            <tr>
                <th>Actividad</th>
                <th>Sub-actividad</th>
                <th>Día</th>
                <th>Inicio</th>
                <th>Término</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($programmingProposal->details as $detail)
                <tr>
                    <td>
                        <b>{{ $detail->activity?->activity_name }}</b>
                        @if ($detail->activity?->motherActivity)
                            - [{{ $detail->activity->motherActivity->description }}]
                        @endif
                        @if ($detail->activity?->process)
                            - <p style="color:red;display:inline">[{{ $detail->activity->process->name }}]</p>
                        @endif
                    </td>
                    <td>
                        @if ($detail->subactivity)
                            {{ $detail->subactivity->sub_activity_name }}
                        @endif
                    </td>
                    <td>{{ $detail->getDayAttribbute() }}</td>
                    <td>{{ $detail->start_hour }}</td>
                    <td>{{ $detail->end_hour }}</td>
                    <td>
                        @if ($programmingProposal->employeeCanModify($programmingProposal) == 1)
                            <form method="POST"
                                action="{{ route('medical_programmer.programming_proposal.details.destroy', $detail) }}"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                    onclick="return confirm('¿Está seguro de eliminar la información?');">
                                    <span class="fas fa-trash-alt" aria-hidden="true"></span>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
        <fieldset class="form-group col col-md-6">
            <div class="table-responsive">
                <table class="table table-responsive">
                    <tr>
                        <td>
                            <span data-feather="square" style="color:#3757E9"></span> Propuesto
                        </td>
                        <td>
                            <span data-feather="square" style="color:#85C1E9"></span> Anterior
                        </td>
                        <td>
                            <span data-feather="square" style="color:#F8CDC4"></span> Otros contratos
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>

        <fieldset class="form-group col col-md-6">
            <div class="d-flex flex-row-reverse">
                @if ($programmingProposal->contract)
                    <div class="p-2"><b>Hrs: {{ $total_hours }} /
                            {{ $programmingProposal->contract->weekly_hours }}</b></div>
                @else
                    <div class="p-2"><b>Hrs: No hay contrato cargado</b></div>
                @endif
            </div>
        </fieldset>
    </div>

    <html lang='en'>

    <head>
        <meta charset='utf-8' />
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.7.0/main.min.css' rel='stylesheet' />
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.7.0/main.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    allDaySlot: false,
                    firstDay: 1,
                    initialDate: '{{ $programmingProposal->start_date->format('Y-m-d') }}',
                    slotMinTime: "08:00:00",
                    timeFormat: 'HH:mm',
                    locale: 'es',
                    events: [

                        // última ficha aceptada
                        @foreach ($last_programmed_days as $programmed_day)
                            @if ($programmed_day['data']->subactivity)
                                {
                                    title: '{{ $programmed_day['data']->subactivity->sub_activity_name }}',
                                    start: '{{ $programmed_day['start_date'] }}',
                                    end: '{{ $programmed_day['end_date'] }}',
                                    color: '#85C1E9'
                                },
                            @else
                                {
                                    title: '{{ $programmed_day['data']->activity->activity_name }}',
                                    start: '{{ $programmed_day['start_date'] }}',
                                    end: '{{ $programmed_day['end_date'] }}',
                                    color: '#85C1E9'
                                },
                            @endif
                        @endforeach

                        // ficha
                        @foreach ($programmed_days as $programmed_day)
                            @if ($programmed_day['data']->subactivity)
                                {
                                    title: '{{ $programmed_day['data']->subactivity->sub_activity_name }}',
                                    start: '{{ $programmed_day['start_date'] }}',
                                    end: '{{ $programmed_day['end_date'] }}'
                                },
                            @else
                                {
                                    title: '{{ $programmed_day['data']->activity->activity_name }}',
                                    start: '{{ $programmed_day['start_date'] }}',
                                    end: '{{ $programmed_day['end_date'] }}'
                                },
                            @endif
                        @endforeach

                        // otras ficha de la persona
                        @foreach ($other_contracts_info as $programmed_day)
                            @if ($programmed_day['data']->subactivity)
                                {
                                    title: '{{ $programmed_day['data']->subactivity->sub_activity_name }}',
                                    start: '{{ $programmed_day['start_date'] }}',
                                    end: '{{ $programmed_day['end_date'] }}',
                                    color: '#F8CDC4'
                                },
                            @else
                                {
                                    title: '{{ $programmed_day['data']->activity->activity_name }}',
                                    start: '{{ $programmed_day['start_date'] }}',
                                    end: '{{ $programmed_day['end_date'] }}',
                                    color: '#F8CDC4'
                                },
                            @endif
                        @endforeach

                    ],
                });

                calendar.render();
            });
        </script>
    </head>

    <body>
        <div id='calendar'></div>
    </body>

    </html>

    <br>

    <div class="card bg-light mb-3">
        <div class="card-header">Flujo de firmas</div>
        <div class="card-body">

            <!-- <p class="card-text">Flujo de firmas</p> -->
            <table class="card-table table table-sm table-bordered small">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programmingProposal->signatureFlows as $signatureFlow)
                        @if ($signatureFlow->status == 'Solicitud rechazada')
                            <tr class="bg-danger">
                            @else
                            <tr class="bg-light">
                        @endif
                        <td>{{ $signatureFlow->signature_date }}</td>
                        <td>{{ $signatureFlow->user?->OfficialFullName }}</td>
                        <td>{{ $signatureFlow->type }}</td>
                        <td>{{ $signatureFlow->status }}</td>
                        <td>{{ $signatureFlow->observation }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="card bg-light mb-3">
        <div class="card-header">Administración</div>
        <div class="card-body">

            <form method="POST" class="form-horizontal"
                action="{{ route('medical_programmer.programming_proposal.store_confirmation', $programmingProposal) }}">
                @csrf
                @method('PUT')

                <p class="card-text">Observaciones</p>
                <textarea name="observation" class="form-control" rows="3" cols="80"></textarea>

                <div class="row">
                    <fieldset class="form-group col col-md-6">
                    </fieldset>
                    <fieldset class="form-group col col-md-6">
                        <div class="d-flex flex-row-reverse bd-highlight">
                            @if ($programmingProposal->employeeCanModify($programmingProposal) == 1)
                                <button type="submit" id="accept_button" class="form-control btn btn-success"
                                    onclick="return confirm('Al confirmar, la información que usted envía deberá ser visada por la subdirección médica ¿Está seguro de confirmar la información?');">
                                    Confirmar
                                </button>
                                <button type="submit" id="reject_button" class="form-control btn btn-danger"
                                    onclick="return confirm('¿Está seguro de rechazar la solicitud?');">
                                    Rechazar
                                </button>

                                <input type="hidden" id="buttonaction" name="buttonaction" value="accept_button">
                            @else
                                <button type="submit" name="accept_button" class="form-control btn btn-success"
                                    disabled>
                                    Confirmar
                                </button>
                                <button type="submit" name="reject_button" class="form-control btn btn-danger" disabled>
                                    Rechazar
                                </button>
                            @endif
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        $("#reject_button").click(function(event) {
            $('#buttonaction').val("reject_button");
        });
    </script>
@endsection
