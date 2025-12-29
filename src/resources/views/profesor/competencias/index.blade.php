@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Competencias de Alumnos</h1>
            <p class="text-gray-600">Visualiza y completa los planes de carrera de tus alumnos.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Competencia</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado Info</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($competencias as $competencia)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                        {{ substr($competencia->alumno->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $competencia->alumno->nombre }} {{ $competencia->alumno->apellido }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $competencia->nombre }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ $competencia->fecha->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($competencia->plan_carrera && $competencia->tiempo_objetivo)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i> Completo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-circle"></i> Pendiente Info
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('competencias.edit', $competencia->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Completar información">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('competencias.destroy', $competencia->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta competencia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-medal text-4xl mb-3 opacity-20"></i>
                                <p>No hay competencias registradas por los alumnos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
