@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Entrenamientos', 'url' => route('entrenamientos.index')],
        ['label' => 'Editar Entrenamiento']
    ]])
@endsection

@section('content')
@php
    $tieneResultados = $entrenamiento->resultados()->exists();
@endphp
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Entrenamiento</h1>
            <p class="text-gray-600">Actualiza los detalles de la sesión</p>
        </div>
        <a href="{{ route('entrenamientos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden"
         x-data="{
            distancia: {{ old('distanciaTotal', $entrenamiento->distanciaTotal ?? 0) }},
            tiempo: {{ old('tiempoTotal', $entrenamiento->tiempoTotal ?? 0) }},
            estimando: false,
            estimar() {
                this.estimando = true;
                const cal = document.getElementsByName('contenidoPersonalizado[calentamiento]')[0].value;
                const tra = document.getElementsByName('contenidoPersonalizado[trabajo_principal]')[0].value;
                const enf = document.getElementsByName('contenidoPersonalizado[enfriamiento]')[0].value;

                fetch('{{ route('entrenamientos.estimar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        contenidoPersonalizado: {
                            calentamiento: cal,
                            trabajo_principal: tra,
                            enfriamiento: enf
                        }
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.distancia) this.distancia = data.distancia;
                    if (data.tiempo) this.tiempo = data.tiempo;
                    this.estimando = false;
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al estimar métricas');
                    this.estimando = false;
                });
            }
         }">
        <form action="{{ route('entrenamientos.update', $entrenamiento->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="titulo" class="text-sm font-semibold text-gray-700">Título del Entrenamiento</label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $entrenamiento->titulo) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Ej. Sesión de Pierna">
                    @error('titulo')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fecha" class="text-sm font-semibold text-gray-700">Fecha Programada</label>
                    <input type="date" name="fecha" id="fecha"
                           value="{{ old('fecha', \Carbon\Carbon::parse($entrenamiento->fecha)->format('Y-m-d')) }}" required
                           {{ $tieneResultados ? 'disabled' : '' }}
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition {{ $tieneResultados ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    @if($tieneResultados)
                        <input type="hidden" name="fecha" value="{{ \Carbon\Carbon::parse($entrenamiento->fecha)->format('Y-m-d') }}">
                        <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            La fecha no se puede cambiar porque ya hay devoluciones.
                        </p>
                    @endif
                    @error('fecha')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100/50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-blue-900 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        Estimaciones de Distancia y Tiempo
                    </h3>
                    <button type="button" @click="estimar()"
                            :disabled="estimando"
                            class="text-xs bg-white border border-blue-200 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition flex items-center gap-2 disabled:opacity-50">
                        <i class="fas fa-robot" :class="estimando ? 'animate-pulse' : ''"></i>
                        <span x-text="estimando ? 'Estimando...' : 'Estimar con IA'"></span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-500 uppercase">Distancia Total (km)</label>
                        <div class="relative">
                            <input type="number" step="0.1" name="distanciaTotal" x-model="distancia"
                                   class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">km</span>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-500 uppercase">Tiempo Total (min)</label>
                        <div class="relative">
                            <input type="number" name="tiempoTotal" x-model="tiempo"
                                   class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">min</span>
                        </div>
                    </div>
                </div>
                <p class="text-[10px] text-blue-600 mt-2 italic">
                    <i class="fas fa-info-circle mr-1"></i>
                    Las estimaciones ayudan al alumno a planificar su sesión.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6" x-data="{
                selectedAlumnos: {{ json_encode(old('alumnos', $entrenamiento->alumnos->pluck('id')->toArray())) }},
                selectedGrupos: {{ json_encode(old('grupos', $entrenamiento->grupos->pluck('id')->toArray())) }},
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
                    <label for="plantillaId" class="text-sm font-semibold text-gray-700">Plantilla (Opcional)</label>
                    <select name="plantillaId" id="plantillaId"
                            {{ $entrenamiento->plantillaId ? 'disabled' : '' }}
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition {{ $entrenamiento->plantillaId ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        <option value="">Selecciona una plantilla</option>
                        @foreach($plantillas as $plantilla)
                            <option value="{{ $plantilla->id }}" {{ old('plantillaId', $entrenamiento->plantillaId) == $plantilla->id ? 'selected' : '' }}>
                                {{ $plantilla->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if($entrenamiento->plantillaId)
                        <p class="text-xs text-gray-500">La plantilla no se puede cambiar después de la creación.</p>
                        <input type="hidden" name="plantillaId" value="{{ $entrenamiento->plantillaId }}">
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
                          placeholder="Ej. Mantener una hidratación constante durante la sesión...">{{ old('observaciones', $entrenamiento->observaciones) }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            @php
                $oldContenido = old('contenidoPersonalizado');
                $contenido = $oldContenido ?? $entrenamiento->contenidoPersonalizado;

                $calentamientoRaw = $contenido['calentamiento'] ?? '';
                $trabajoRaw = $contenido['trabajo_principal'] ?? '';
                $enfriamientoRaw = $contenido['enfriamiento'] ?? '';

                $calentamientoInitial = is_array($calentamientoRaw) ? implode("\n", $calentamientoRaw) : $calentamientoRaw;
                $trabajoInitial = is_array($trabajoRaw) ? implode("\n", $trabajoRaw) : $trabajoRaw;
                $enfriamientoInitial = is_array($enfriamientoRaw) ? implode("\n", $enfriamientoRaw) : $enfriamientoRaw;
            @endphp

            <div class="grid grid-cols-1 gap-6">
                <!-- Calentamiento -->
                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <h3 class="text-orange-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-fire"></i> Calentamiento
                    </h3>
                    @if($tieneResultados)
                        <p class="text-[10px] text-amber-600 mb-2 italic">No se pueden editar los ejercicios una vez recibidas devoluciones.</p>
                    @endif
                    <textarea name="contenidoPersonalizado[calentamiento]" rows="3"
                              {{ $tieneResultados ? 'disabled' : '' }}
                              class="w-full px-4 py-2 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition text-sm {{ $tieneResultados ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                              placeholder="Describe el calentamiento...">{{ $calentamientoInitial }}</textarea>
                </div>

                <!-- Trabajo Principal -->
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <h3 class="text-blue-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-dumbbell"></i> Trabajo Principal
                    </h3>
                    <textarea name="contenidoPersonalizado[trabajo_principal]" rows="5"
                              {{ $tieneResultados ? 'disabled' : '' }}
                              class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm {{ $tieneResultados ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                              placeholder="Describe el trabajo principal...">{{ $trabajoInitial }}</textarea>
                </div>

                <!-- Enfriamiento -->
                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                    <h3 class="text-green-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-wind"></i> Enfriamiento
                    </h3>
                    <textarea name="contenidoPersonalizado[enfriamiento]" rows="3"
                              {{ $tieneResultados ? 'disabled' : '' }}
                              class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 outline-none transition text-sm {{ $tieneResultados ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                              placeholder="Describe el enfriamiento...">{{ $enfriamientoInitial }}</textarea>
                </div>
            </div>
            @if($tieneResultados)
                <input type="hidden" name="contenidoPersonalizado[calentamiento]" value="{{ $calentamientoInitial }}">
                <input type="hidden" name="contenidoPersonalizado[trabajo_principal]" value="{{ $trabajoInitial }}">
                <input type="hidden" name="contenidoPersonalizado[enfriamiento]" value="{{ $enfriamientoInitial }}">
            @endif
            @error('contenidoPersonalizado')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('entrenamientos.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                    Actualizar Entrenamiento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
