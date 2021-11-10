@extends('layouts.app')

@section('content')

<h3 class="mb-3">Listado de Servicios
    <a class="btn btn-primary mb-2" href="{{ route('medical_programmer.services.create') }}">
        <i class="fas fa-plus"></i> Agregar nueva
    </a>
</h3>

<table class="table table-sm table-borderer">
    <thead>
        <tr>
            <th>Id</th>
            <th>Código de servicio</th>
            <th>Servicio</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach( $services as $service )
        <tr>
            <td>{{ $service->id }}</td>
            <td>{{ $service->service_code }}</td>
            <td>{{ $service->service_name }}</td>
            <td>
      				<a href="{{ route('medical_programmer.services.edit', $service) }}"
      					class="btn btn-sm btn-outline-secondary">
      					<span class="fas fa-edit" aria-hidden="true"></span>
      				</a>
      				<form method="POST" action="{{ route('medical_programmer.services.destroy', $service) }}" class="d-inline">
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
