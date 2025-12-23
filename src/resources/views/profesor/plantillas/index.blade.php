@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Plantillas</h1>
            <p class="text-gray-600">Modelos de entrenamiento reutilizables</p>
        </div>
        <a href="{{ route('plantillas.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nueva Plantilla
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($plantillas as $plantilla)
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">{{ $plantilla->nombre }}</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('plantillas.edit', $plantilla->id) }}" class="text-gray-400 hover:text-blue-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('plantillas.destroy', $plantilla->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta plantilla?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $plantilla->descripcion ?? 'Sin descripción' }}</p>
                <div class="p-3 bg-gray-50 rounded-lg text-xs font-mono text-gray-500 overflow-hidden h-24">
                    @if(is_array($plantilla->contenido))
                        <div class="space-y-1">
                            @foreach($plantilla->contenido['calentamiento'] ?? [] as $ej)
                                <span class="inline-block bg-orange-100 text-orange-700 px-1 rounded">C: {{ $ej }}</span>
                            @endforeach
                            @foreach($plantilla->contenido['trabajo_principal'] ?? [] as $ej)
                                <span class="inline-block bg-blue-100 text-blue-700 px-1 rounded">T: {{ $ej }}</span>
                            @endforeach
                            @foreach($plantilla->contenido['enfriamiento'] ?? [] as $ej)
                                <span class="inline-block bg-green-100 text-green-700 px-1 rounded">E: {{ $ej }}</span>
                            @endforeach
                        </div>
                    @else
                        {{ $plantilla->contenido }}
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 bg-white rounded-xl shadow-md border border-gray-100">
                No hay plantillas creadas
            </div>
        @endforelse
    </div>
</div>
@endsection
