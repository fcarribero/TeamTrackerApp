@extends('layouts.dashboard')

@section('content')
@php
    $oldContenido = old('contenidoPersonalizado') ? json_decode(old('contenidoPersonalizado'), true) : null;
    $calentamientoInitial = $oldContenido['calentamiento'] ?? (isset($selectedPlantilla) ? ($selectedPlantilla->contenido['calentamiento'] ?? []) : ["20' CCL"]);
    $trabajoInitial = $oldContenido['trabajo_principal'] ?? (isset($selectedPlantilla) ? ($selectedPlantilla->contenido['trabajo_principal'] ?? []) : []);
    $enfriamientoInitial = $oldContenido['enfriamiento'] ?? (isset($selectedPlantilla) ? ($selectedPlantilla->contenido['enfriamiento'] ?? []) : ["10' CCL"]);
@endphp
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Programar Entrenamiento</h1>
            <p class="text-gray-600">Asigna un entrenamiento a un alumno o grupo</p>
        </div>
        <a href="{{ route('entrenamientos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('entrenamientos.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="titulo" class="text-sm font-semibold text-gray-700">Título del Entrenamiento</label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $selectedPlantilla->nombre ?? '') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Ej. Sesión de Pierna">
                    @error('titulo')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fecha" class="text-sm font-semibold text-gray-700">Fecha Programada</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fecha')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6" x-data="{
                selectedAlumnos: {{ json_encode((array)old('alumnos', request('alumnoId') ? [request('alumnoId')] : [])) }},
                selectedGrupos: {{ json_encode((array)old('grupos', [])) }},
                toggleAlumno(id) {
                    const index = this.selectedAlumnos.indexOf(id);
                    if (index === -1) {
                        this.selectedAlumnos.push(id);
                    } else {
                        this.selectedAlumnos.splice(index, 1);
                    }
                },
                toggleGrupo(id) {
                    const index = this.selectedGrupos.indexOf(id);
                    if (index === -1) {
                        this.selectedGrupos.push(id);
                    } else {
                        this.selectedGrupos.splice(index, 1);
                    }
                }
            }">
                <div class="md:col-span-1 space-y-4">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-users text-blue-600"></i>
                        Asignar a Grupos
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($grupos as $grupo)
                            <div @click="toggleGrupo('{{ $grupo->id }}')"
                                 :class="selectedGrupos.includes('{{ $grupo->id }}') ? 'ring-2 ring-offset-1 ring-blue-500 shadow-md' : 'opacity-80 hover:opacity-100'"
                                 class="relative cursor-pointer rounded-lg p-3 transition-all duration-200 border border-gray-100 flex items-center gap-3"
                                 style="background-color: {{ $grupo->color ?? '#f3f4f6' }}20; border-left: 4px solid {{ $grupo->color ?? '#9ca3af' }}">
                                <input type="checkbox" name="grupos[]" value="{{ $grupo->id }}"
                                       x-model="selectedGrupos"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 pointer-events-none">
                                <span class="text-sm font-bold text-gray-800 truncate">{{ $grupo->nombre }}</span>
                            </div>
                        @endforeach
                    </div>
                    @error('grupos')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-1 space-y-3">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-user text-gray-500 text-xs"></i>
                        Asignar a Alumnos Individuales
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
                        @foreach($alumnos as $alumno)
                            <div @click="toggleAlumno('{{ $alumno->id }}')"
                                 :class="selectedAlumnos.includes('{{ $alumno->id }}') ? 'bg-blue-50 border-blue-400 ring-1 ring-blue-400' : 'bg-gray-50 border-gray-200 hover:bg-white hover:border-gray-300'"
                                 class="cursor-pointer rounded-md px-2 py-1.5 border transition-all flex items-center gap-2">
                                <input type="checkbox" name="alumnos[]" value="{{ $alumno->id }}"
                                       x-model="selectedAlumnos"
                                       class="w-3 h-3 text-blue-600 border-gray-300 rounded-sm focus:ring-blue-500 pointer-events-none">
                                <span class="text-xs text-gray-700 truncate">{{ $alumno->nombre }}</span>
                            </div>
                        @endforeach
                    </div>
                    @error('alumnos')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="plantillaId" class="text-sm font-semibold text-gray-700">Plantilla</label>
                    @if(isset($selectedPlantilla))
                        <div class="w-full px-4 py-2 border border-blue-100 bg-blue-50 text-blue-700 rounded-lg flex items-center justify-between">
                            <span class="font-medium">{{ $selectedPlantilla->nombre }}</span>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <input type="hidden" name="plantillaId" value="{{ $selectedPlantilla->id }}">
                    @else
                        <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-500 rounded-lg flex items-center justify-between italic">
                            <span>Sin plantilla seleccionada</span>
                            <i class="fas fa-info-circle"></i>
                        </div>
                    @endif
                    @error('plantillaId')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="observaciones" class="text-sm font-semibold text-gray-700">Observaciones para el Alumno</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                          placeholder="Ej. Mantener una hidratación constante durante la sesión...">{{ old('observaciones', $selectedPlantilla->observaciones ?? '') }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{
                calentamiento: {{ json_encode($calentamientoInitial) }},
                trabajo: {{ json_encode($trabajoInitial) }},
                enfriamiento: {{ json_encode($enfriamientoInitial) }},
                inputCalentamiento: '',
                inputTrabajo: '',
                inputEnfriamiento: '',
                addExercise(type) {
                    let val = this['input' + type.charAt(0).toUpperCase() + type.slice(1)];
                    if (val.trim()) {
                        this[type].push(val.trim());
                        this['input' + type.charAt(0).toUpperCase() + type.slice(1)] = '';
                        this.updateHidden();
                    }
                },
                removeExercise(type, index) {
                    this[type].splice(index, 1);
                    this.updateHidden();
                },
                updateHidden() {
                    this.$refs.contenidoInput.value = JSON.stringify({
                        calentamiento: this.calentamiento,
                        trabajo_principal: this.trabajo,
                        enfriamiento: this.enfriamiento
                    });
                }
            }" class="space-y-6" x-init="updateHidden()">
                <input type="hidden" name="contenidoPersonalizado" x-ref="contenidoInput">

                <div class="grid grid-cols-1 gap-6">
                    <!-- Calentamiento -->
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                        <h3 class="text-orange-800 font-bold mb-3 flex items-center gap-2">
                            <i class="fas fa-fire"></i> Calentamiento
                        </h3>
                        <div class="flex gap-2 mb-3">
                            <input type="text" x-model="inputCalentamiento" @keydown.enter.prevent="addExercise('calentamiento')"
                                   class="flex-1 px-3 py-1.5 border border-orange-200 rounded-lg outline-none focus:ring-2 focus:ring-orange-500 transition text-sm"
                                   placeholder="Nuevo ejercicio de calentamiento...">
                            <button type="button" @click="addExercise('calentamiento')" class="bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(ex, index) in calentamiento" :key="index">
                                <span class="bg-white text-orange-700 px-3 py-1 rounded-full text-sm border border-orange-200 flex items-center gap-2">
                                    <span x-text="ex"></span>
                                    <button type="button" @click="removeExercise('calentamiento', index)" class="text-orange-400 hover:text-orange-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Trabajo Principal -->
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <h3 class="text-blue-800 font-bold mb-3 flex items-center gap-2">
                            <i class="fas fa-dumbbell"></i> Trabajo Principal
                        </h3>
                        <div class="flex gap-2 mb-3">
                            <input type="text" x-model="inputTrabajo" @keydown.enter.prevent="addExercise('trabajo')"
                                   class="flex-1 px-3 py-1.5 border border-blue-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"
                                   placeholder="Nuevo ejercicio principal...">
                            <button type="button" @click="addExercise('trabajo')" class="bg-blue-500 text-white px-3 py-1.5 rounded-lg hover:bg-blue-600 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(ex, index) in trabajo" :key="index">
                                <span class="bg-white text-blue-700 px-3 py-1 rounded-full text-sm border border-blue-200 flex items-center gap-2">
                                    <span x-text="ex"></span>
                                    <button type="button" @click="removeExercise('trabajo', index)" class="text-blue-400 hover:text-blue-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Enfriamiento -->
                    <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                        <h3 class="text-green-800 font-bold mb-3 flex items-center gap-2">
                            <i class="fas fa-wind"></i> Enfriamiento
                        </h3>
                        <div class="flex gap-2 mb-3">
                            <input type="text" x-model="inputEnfriamiento" @keydown.enter.prevent="addExercise('enfriamiento')"
                                   class="flex-1 px-3 py-1.5 border border-green-200 rounded-lg outline-none focus:ring-2 focus:ring-green-500 transition text-sm"
                                   placeholder="Nuevo ejercicio de enfriamiento...">
                            <button type="button" @click="addExercise('enfriamiento')" class="bg-green-500 text-white px-3 py-1.5 rounded-lg hover:bg-green-600 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(ex, index) in enfriamiento" :key="index">
                                <span class="bg-white text-green-700 px-3 py-1 rounded-full text-sm border border-green-200 flex items-center gap-2">
                                    <span x-text="ex"></span>
                                    <button type="button" @click="removeExercise('enfriamiento', index)" class="text-green-400 hover:text-green-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
                @error('contenidoPersonalizado')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('entrenamientos.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                    Programar Entrenamiento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
