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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="dni" class="text-sm font-semibold text-gray-700">DNI / ID</label>
                    <input type="text" name="dni" id="dni" value="{{ old('dni', $alumno->dni) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Número de identificación">
                    @error('dni')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="hidden md:block"></div>

                <div class="space-y-2">
                    <label for="nombre" class="text-sm font-semibold text-gray-700">Nombre</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $alumno->nombre) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Nombre">
                    @error('nombre')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="apellido" class="text-sm font-semibold text-gray-700">Apellido</label>
                    <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $alumno->apellido) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Apellido">
                    @error('apellido')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fechaNacimiento" class="text-sm font-semibold text-gray-700">Fecha de Nacimiento</label>
                    <input type="date" name="fechaNacimiento" id="fechaNacimiento"
                           value="{{ old('fechaNacimiento', \Carbon\Carbon::parse($alumno->fechaNacimiento)->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fechaNacimiento')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="sexo" class="text-sm font-semibold text-gray-700">Sexo</label>
                    <select name="sexo" id="sexo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="masculino" {{ old('sexo', $alumno->sexo) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="femenino" {{ old('sexo', $alumno->sexo) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                    </select>
                    @error('sexo')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-100 space-y-6">
                <h3 class="text-blue-900 font-bold flex items-center gap-2 border-b border-blue-100 pb-2">
                    <i class="fas fa-id-card"></i>
                    Información Médica y Social
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="obra_social" class="text-sm font-semibold text-gray-700">Obra Social / Plan</label>
                        <input type="text" name="obra_social" id="obra_social" value="{{ old('obra_social', $alumno->obra_social) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej. OSDE 210">
                    </div>

                    <div class="space-y-2">
                        <label for="numero_socio" class="text-sm font-semibold text-gray-700">Número de Socio</label>
                        <input type="text" name="numero_socio" id="numero_socio" value="{{ old('numero_socio', $alumno->numero_socio) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Número de afiliado">
                    </div>

                    <div class="space-y-2">
                        <label for="certificado_medico" class="text-sm font-semibold text-gray-700">Certificado Médico (PDF/Imagen)</label>
                        <input type="file" name="certificado_medico" id="certificado_medico"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                        <p class="text-[10px] text-gray-500 italic">Dejar vacío para mantener el actual.</p>
                        @if($alumno->certificado_medico)
                            <div class="mt-2 flex items-center gap-2 text-xs text-blue-600">
                                <i class="fas fa-file-alt"></i>
                                <a href="{{ Storage::url($alumno->certificado_medico) }}" target="_blank" class="hover:underline font-bold">Ver Certificado Actual</a>
                            </div>
                        @endif
                        @error('certificado_medico')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="vencimiento_certificado" class="text-sm font-semibold text-gray-700">Vencimiento del Certificado</label>
                        <input type="date" name="vencimiento_certificado" id="vencimiento_certificado"
                               value="{{ old('vencimiento_certificado', $alumno->vencimiento_certificado ? \Carbon\Carbon::parse($alumno->vencimiento_certificado)->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
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
