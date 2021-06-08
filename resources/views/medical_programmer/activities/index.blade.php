@extends('layouts.app')

@section('content')

<h3 class="mb-3">Listado de Actividades</h3>

<a class="btn btn-primary mb-3" href="{{ route('medical_programmer.activities.create') }}">
    <i class="fas fa-plus"></i> Agregar nueva
</a>

<table class="table table-sm table-borderer">
    <thead>
        <tr>
            <th>id_actividad</th>
            <th>Actividad Madre</th>
            <th>Tipo de actividad</th>
            <th>Especialidad</th>
            <th>Rendimiento</th>
            <th>Programable</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach( $activities as $activity )
        <tr>
            <td>{{ $activity->id_activity }}</td>
            <td>@if($activity->motherActivity){{ $activity->motherActivity->description }}@endif</td>
            <td>@if($activity->activityType){{ $activity->activityType->name }}@endif</td>
            <td>{{ $activity->activity_name }}</td>
            <td>@if($activity->performance) R @else NR @endif</td>
            <td>@if($activity->programmable) Sí @else No @endif</td>
            <td>
      				<a href="{{ route('medical_programmer.activities.edit', $activity) }}"
      					class="btn btn-sm btn-outline-secondary">
      					<span class="fas fa-edit" aria-hidden="true"></span>
      				</a>
      				<form method="POST" action="{{ route('medical_programmer.activities.destroy', $activity) }}" class="d-inline">
      					@csrf
      					@method('DELETE')
      					<button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('¿Está seguro de eliminar la información?');">
      						<span class="fas fa-trash-alt" aria-hidden="true"></span>
      					</button>
      				</form>
      			</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@section('custom_js')

@endsection
