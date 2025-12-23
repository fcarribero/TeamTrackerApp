@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Entrenamientos</h1>
            <p class="text-gray-600">Calendario y asignación de sesiones</p>
        </div>
        <a href="{{ route('entrenamientos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Asignar Entrenamiento
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <div class="space-y-4">
            @forelse($entrenamientos as $entrenamiento)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="bg-blue-500 p-3 rounded-lg text-white">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">{{ $entrenamiento->titulo }}</h3>
                        <p class="text-sm text-gray-600">
                            @if($entrenamiento->alumnos->isNotEmpty())
                                {{ $entrenamiento->alumnos->pluck('nombre')->implode(', ') }}
                            @elseif($entrenamiento->grupos->isNotEmpty())
                                Grupos: {{ $entrenamiento->grupos->pluck('nombre')->implode(', ') }}
                            @else
                                General
                            @endif
                            • {{ \Carbon\Carbon::parse($entrenamiento->fecha)->format('d/m/Y') }}
                        </p>
                        @if($entrenamiento->resultados_count > 0)
                            <a href="{{ route('entrenamientos.show', $entrenamiento->id) }}" class="inline-flex items-center gap-1 mt-1 text-xs font-bold text-blue-600 hover:underline">
                                <i class="fas fa-comment-dots"></i>
                                {{ $entrenamiento->resultados_count }} {{ $entrenamiento->resultados_count == 1 ? 'devolución' : 'devoluciones' }}
                            </a>
                        @else
                            <p class="text-[10px] text-gray-400 mt-1 italic">Sin devoluciones aún</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('entrenamientos.edit', $entrenamiento->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('entrenamientos.destroy', $entrenamiento->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este entrenamiento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">No hay entrenamientos programados</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
