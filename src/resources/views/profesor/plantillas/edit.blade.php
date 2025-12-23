@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Plantilla</h1>
            <p class="text-gray-600">Actualiza la plantilla {{ $plantilla->nombre }}</p>
        </div>
        <a href="{{ route('plantillas.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('plantillas.update', $plantilla->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="nombre" class="text-sm font-semibold text-gray-700">Nombre de la Plantilla</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $plantilla->nombre) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                       placeholder="Ej. Rutina Fuerza A">
                @error('nombre')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="descripcion" class="text-sm font-semibold text-gray-700">Descripción Corta</label>
                <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion', $plantilla->descripcion) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                       placeholder="Ej. Enfoque en hipertrofia de pierna">
                @error('descripcion')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="observaciones" class="text-sm font-semibold text-gray-700">Observaciones para el Alumno</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                          placeholder="Ej. Mantener una hidratación constante durante la sesión...">{{ old('observaciones', $plantilla->observaciones) }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{
                calentamiento: {{ json_encode(is_array($plantilla->contenido) ? ($plantilla->contenido['calentamiento'] ?? []) : []) }},
                trabajo: {{ json_encode(is_array($plantilla->contenido) ? ($plantilla->contenido['trabajo_principal'] ?? []) : []) }},
                enfriamiento: {{ json_encode(is_array($plantilla->contenido) ? ($plantilla->contenido['enfriamiento'] ?? []) : []) }},
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
                <input type="hidden" name="contenido" x-ref="contenidoInput">

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
                @error('contenido')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('plantillas.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                    Actualizar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
