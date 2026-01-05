@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Grupos']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Grupos</h1>
            <p class="text-gray-600">Gestiona tus grupos de entrenamiento</p>
        </div>
        <a href="{{ route('grupos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nuevo Grupo
        </a>
    </div>

    <div id="tour-lista-grupos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($grupos as $grupo)
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                    <div style="background-color: {{ $grupo->color ?? '#3b82f6' }};" class="w-4 h-4 rounded-full"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $grupo->nombre }}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $grupo->descripcion ?? 'Sin descripción' }}</p>
                <div class="pt-4 border-t border-gray-50 flex items-center justify-between text-sm text-gray-500">
                    <span><i class="fas fa-users mr-2"></i> {{ count($grupo->alumnos ?? []) }} Alumnos</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('grupos.edit', $grupo->id) }}" class="text-blue-500 hover:text-blue-700 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este grupo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-md p-12 text-center text-gray-500 border border-gray-100">
                No hay grupos registrados
            </div>
        @endforelse
    </div>
</div>
@endsection
