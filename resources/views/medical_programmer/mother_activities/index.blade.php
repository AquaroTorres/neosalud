@extends('layouts.app')

@section('content')

<h2 class="mb-3">Listado de Actividades Madre
    <a class="btn btn-primary mb-2" href="{{ route('medical_programmer.mother_activities.create') }}">
        <i class="fas fa-plus"></i> Agregar nueva
    </a>
</h2>

<table class="table table-sm table-borderer">
    <thead>
        <tr>
            <th>Id</th>
            <th>Actividad Madre</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach( $motherActivities as $MotherActivity )
        <tr>
            <td>{{ $MotherActivity->id }}</td>
            <td>{{ $MotherActivity->description }}</td>
            <td>
      				<a href="{{ route('medical_programmer.mother_activities.edit', $MotherActivity) }}"
      					class="btn btn-sm btn-outline-secondary">
      					<span class="fas fa-edit" aria-hidden="true"></span>
      				</a>
      				<form method="POST" action="{{ route('medical_programmer.mother_activities.destroy', $MotherActivity) }}" class="d-inline">
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
