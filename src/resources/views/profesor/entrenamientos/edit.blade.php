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

    $initialAlumnosData = $entrenamiento->alumnos->map(function($alumno) {
        return [
            'id' => $alumno->id,
            'nombre_completo' => $alumno->nombre . ' ' . $alumno->apellido,
            'image' => $alumno->image ? asset('storage/' . $alumno->image) : null,
            'inicial' => substr($alumno->nombre, 0, 1)
        ];
    })->toArray();
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
         x-data="entrenamientoEditor({
            distancia: {{ old('distanciaTotal', $entrenamiento->distanciaTotal ?? 0) }},
            tiempo: {{ old('tiempoTotal', $entrenamiento->tiempoTotal ?? 0) }},
            selectedAlumnos: {{ json_encode(old('alumnos', $entrenamiento->alumnos->pluck('id')->toArray())) }},
            selectedAlumnosData: {{ json_encode($initialAlumnosData) }},
            selectedGrupos: {{ json_encode(old('grupos', $entrenamiento->grupos->pluck('id')->toArray())) }},
            generalGroupId: '{{ $generalGroupId }}'
         })">
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

            <div class="grid grid-cols-1 gap-6">
                <div class="md:col-span-1 space-y-4">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-users text-blue-600"></i>
                        Asignar a Grupos
                    </label>

                    @php
                        $generalGroup = $grupos->where('nombre', 'General')->first();
                        $otherGrupos = $grupos->where('nombre', '!=', 'General');
                    @endphp

                    <div class="space-y-4">
                        @if($generalGroup)
                            <div @click="toggleGrupo('{{ $generalGroup->id }}')"
                                 :class="selectedGrupos.includes('{{ $generalGroup->id }}') ? 'ring-2 ring-offset-2 ring-blue-500 shadow-lg border-blue-200' : 'opacity-80 hover:opacity-100 border-gray-100'"
                                 class="relative cursor-pointer rounded-xl p-4 transition-all duration-300 border-2 flex items-center justify-between bg-blue-50/30">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div>
                                        <span class="text-base font-bold text-gray-900">{{ $generalGroup->nombre }}</span>
                                        <p class="text-xs text-gray-500">Asignar a todos los alumnos del equipo</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="grupos[]" value="{{ $generalGroup->id }}"
                                       x-model="selectedGrupos"
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500 pointer-events-none">
                            </div>
                        @endif

                        <div x-show="!isGeneralSelected()" x-transition class="space-y-3 pt-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">O elegir grupos específicos</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($otherGrupos as $grupo)
                                    <div @click="toggleGrupo('{{ $grupo->id }}')"
                                         :class="selectedGrupos.includes('{{ $grupo->id }}') ? 'ring-2 ring-offset-1 ring-blue-500 shadow-md' : 'opacity-80 hover:opacity-100'"
                                         class="relative cursor-pointer rounded-lg p-3 transition-all duration-200 border border-gray-100 flex items-center gap-3"
                                         style="background-color: {{ $grupo->color ?? '#f3f4f6' }}20; border-left: 4px solid {{ $grupo->color ?? '#9ca3af' }}">
                                        <input type="checkbox" name="grupos[]" value="{{ $grupo->id }}"
                                               x-model="selectedGrupos"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 pointer-events-none">
                                        <x-group-tag :grupo="$grupo" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @error('grupos')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-1 space-y-3" x-show="!isGeneralSelected()" x-transition>
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-user text-blue-600"></i>
                            Asignar a Alumnos Individuales
                        </label>
                        <button type="button" @click="openModal()" class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-plus"></i> Agregar Alumno
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-2 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 min-h-[60px]">
                        <template x-for="alumno in selectedAlumnosData" :key="alumno.id">
                            <div class="bg-white border border-gray-200 rounded-full pl-1 pr-3 py-1 flex items-center gap-2 shadow-sm animate-fade-in">
                                <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-[10px] overflow-hidden">
                                    <img x-show="alumno.image" :src="alumno.image" class="w-full h-full object-cover">
                                    <span x-show="!alumno.image" x-text="alumno.inicial"></span>
                                </div>
                                <span class="text-xs font-medium text-gray-700" x-text="alumno.nombre_completo"></span>
                                <button type="button" @click="removeAlumno(alumno.id)" class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                                <input type="hidden" name="alumnos[]" :value="alumno.id">
                            </div>
                        </template>
                        <p x-show="selectedAlumnosData.length === 0" class="text-xs text-gray-400 italic w-full text-center py-2">No hay alumnos seleccionados individualmente</p>
                    </div>

                    @error('alumnos')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal de Búsqueda -->
                <div x-show="isModalOpen"
                     class="fixed inset-0 z-[100] overflow-y-auto"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal()">
                            <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
                        </div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                            <div class="bg-white px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                        <i class="fas fa-search text-blue-600"></i>
                                        Buscar Alumno
                                    </h3>
                                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-times text-lg"></i>
                                    </button>
                                </div>

                                <div class="relative mb-4">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text"
                                           x-model="searchQuery"
                                           @input.debounce.300ms="search()"
                                           placeholder="Nombre o apellido del alumno..."
                                           class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                           x-ref="searchInput">
                                </div>

                                <div class="max-h-64 overflow-y-auto custom-scrollbar pr-1">
                                    <div x-show="loading" class="flex flex-col items-center py-8">
                                        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-500">Buscando alumnos...</p>
                                    </div>

                                    <div x-show="!loading && results.length > 0" class="space-y-2">
                                        <template x-for="alumno in results" :key="alumno.id">
                                            <div @click="addAlumno(alumno)"
                                                 class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-blue-50 hover:border-blue-200 cursor-pointer transition group">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold overflow-hidden border border-gray-200 shadow-sm">
                                                        <img x-show="alumno.image" :src="alumno.image" class="w-full h-full object-cover">
                                                        <span x-show="!alumno.image" x-text="alumno.inicial"></span>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-900 group-hover:text-blue-700" x-text="alumno.nombre_completo"></p>
                                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider" x-text="selectedAlumnos.includes(alumno.id) ? 'Ya seleccionado' : 'Hacer clic para agregar'"></p>
                                                    </div>
                                                </div>
                                                <div x-show="!selectedAlumnos.includes(alumno.id)" class="text-blue-500 opacity-0 group-hover:opacity-100 transition">
                                                    <i class="fas fa-plus-circle text-xl"></i>
                                                </div>
                                                <div x-show="selectedAlumnos.includes(alumno.id)" class="text-green-500">
                                                    <i class="fas fa-check-circle text-xl"></i>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="!loading && results.length === 0 && searchQuery.length > 2" class="text-center py-8">
                                        <i class="fas fa-user-slash text-gray-300 text-3xl mb-2"></i>
                                        <p class="text-gray-500 italic">No se encontraron alumnos</p>
                                    </div>

                                    <div x-show="!loading && searchQuery.length <= 2" class="text-center py-8">
                                        <i class="fas fa-keyboard text-gray-200 text-3xl mb-2"></i>
                                        <p class="text-gray-400 text-xs">Escribe al menos 3 letras para buscar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                                <button type="button" @click="closeModal()" class="px-6 py-2 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-900 transition shadow-lg">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
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

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('entrenamientoEditor', (config) => ({
                distancia: config.distancia,
                tiempo: config.tiempo,
                estimando: false,
                selectedAlumnos: config.selectedAlumnos,
                selectedAlumnosData: config.selectedAlumnosData,
                selectedGrupos: config.selectedGrupos,
                generalGroupId: config.generalGroupId,
                isModalOpen: false,
                searchQuery: '',
                results: [],
                loading: false,

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
                },

                openModal() {
                    if (this.isGeneralSelected()) return;
                    this.isModalOpen = true;
                    this.$nextTick(() => {
                        this.$refs.searchInput.focus();
                    });
                },

                closeModal() {
                    this.isModalOpen = false;
                    this.searchQuery = '';
                    this.results = [];
                },

                isGeneralSelected() {
                    return this.selectedGrupos.includes(this.generalGroupId);
                },

                async search() {
                    if (this.searchQuery.length <= 2) {
                        this.results = [];
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('alumnos.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                        this.results = await response.json();
                    } catch (error) {
                        console.error('Error al buscar alumnos:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                addAlumno(alumno) {
                    if (this.isGeneralSelected()) return;
                    if (!this.selectedAlumnos.includes(alumno.id)) {
                        this.selectedAlumnos.push(alumno.id);
                        this.selectedAlumnosData.push(alumno);
                    }
                },

                removeAlumno(id) {
                    this.selectedAlumnos = this.selectedAlumnos.filter(aId => aId !== id);
                    this.selectedAlumnosData = this.selectedAlumnosData.filter(a => a.id !== id);
                },

                toggleGrupo(id) {
                    const index = this.selectedGrupos.indexOf(id);
                    if (index === -1) {
                        if (id === this.generalGroupId) {
                            // Si seleccionamos general, vaciamos todo lo demás
                            this.selectedGrupos = [id];
                            this.selectedAlumnos = [];
                            this.selectedAlumnosData = [];
                        } else {
                            // Si seleccionamos otro, quitamos general si estaba
                            if (this.isGeneralSelected()) {
                                this.selectedGrupos = this.selectedGrupos.filter(gId => gId !== this.generalGroupId);
                            }
                            this.selectedGrupos.push(id);
                        }
                    } else {
                        this.selectedGrupos.splice(index, 1);
                    }
                }
            }));
        });
    </script>
@endpush
