@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Alumnos', 'url' => route('alumnos.index')],
        ['label' => 'Editar Alumno']
    ]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Alumno</h1>
            <p class="text-gray-600">Actualiza la información de {{ $alumno->nombre }}</p>
        </div>
        <a href="{{ route('alumnos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-60">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">DNI / ID</label>
                    <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                        {{ $alumno->dni }}
                    </div>
                </div>

                <div class="hidden md:block"></div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Nombre</label>
                    <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                        {{ $alumno->nombre }}
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Apellido</label>
                    <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                        {{ $alumno->apellido }}
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Fecha de Nacimiento</label>
                    <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                        {{ \Carbon\Carbon::parse($alumno->fechaNacimiento)->format('d/m/Y') }}
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Sexo</label>
                    <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                        {{ ucfirst($alumno->sexo ?? '-') }}
                    </div>
                </div>
            </div>

            <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-100 space-y-6 opacity-60">
                <h3 class="text-blue-900 font-bold flex items-center gap-2 border-b border-blue-100 pb-2">
                    <i class="fas fa-id-card"></i>
                    Información Médica y Social (Solo lectura)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full grid grid-cols-1 md:grid-cols-2 gap-6 bg-white/50 p-4 rounded-lg border border-blue-100/50">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Obra Social / Plan</label>
                            <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600">
                                {{ $alumno->obra_social ?: '-' }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Número de Socio</label>
                            <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600">
                                {{ $alumno->numero_socio ?: '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Certificado Médico</label>
                        @if($alumno->certificado_medico)
                            <div class="flex items-center gap-2 text-sm text-blue-600 py-2">
                                <i class="fas fa-file-alt"></i>
                                <a href="{{ Storage::url($alumno->certificado_medico) }}" target="_blank" class="hover:underline font-bold">Ver Certificado</a>
                            </div>
                        @else
                            <div class="text-sm text-gray-500 py-2 italic">No cargado</div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Vencimiento del Certificado</label>
                        <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600">
                            {{ $alumno->vencimiento_certificado ? \Carbon\Carbon::parse($alumno->vencimiento_certificado)->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label for="notas" class="text-sm font-semibold text-gray-700">Notas Adicionales</label>
                <textarea name="notas" id="notas" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                          placeholder="Alguna observación relevante...">{{ old('notas', $alumno->notas) }}</textarea>
                @error('notas')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-4">
                <label class="text-sm font-semibold text-gray-700 block">Asignar a Grupos</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    @php $currentGrupos = $alumno->grupos->pluck('id')->toArray(); @endphp
                    @forelse($grupos as $grupo)
                        <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-100 hover:border-blue-300 transition cursor-pointer">
                            <input type="checkbox" name="grupos[]" value="{{ $grupo->id }}"
                                   {{ in_array($grupo->id, old('grupos', $currentGrupos)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="flex items-center gap-2">
                                <div style="background-color: {{ $grupo->color ?? '#3B82F6' }};" class="w-3 h-3 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $grupo->nombre }}</span>
                            </div>
                        </label>
                    @empty
                        <p class="col-span-full text-center text-gray-500 py-4 italic text-sm">No hay grupos creados</p>
                    @endforelse
                </div>
                @error('grupos')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('alumnos.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                    Actualizar Alumno
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
