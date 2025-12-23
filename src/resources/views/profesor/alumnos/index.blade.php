@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Alumnos</h1>
            <p class="text-gray-600">Gestiona tus alumnos y sus perfiles</p>
        </div>
        <a href="{{ route('alumnos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nuevo Alumno
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Nombre</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Sexo</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Grupos</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Fecha Nacimiento</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($alumnos as $alumno)
                        <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('alumnos.show', $alumno->id) }}'">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                        {{ substr($alumno->nombre, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $alumno->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="capitalize text-gray-600">{{ $alumno->sexo }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($alumno->grupos as $grupo)
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $grupo->color ?? '#3B82F6' }}" title="{{ $grupo->nombre }}"></span>
                                    @empty
                                        <span class="text-xs text-gray-400">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($alumno->fechaNacimiento)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('alumnos.edit', $alumno->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition" onclick="event.stopPropagation()">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('alumnos.destroy', $alumno->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este alumno?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition" onclick="event.stopPropagation()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No hay alumnos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
