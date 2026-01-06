@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Pagos', 'url' => route('pagos.index')],
        ['label' => 'Registrar Pago']
    ]])
@endsection

@section('content')
@php
    $initialAlumnosData = [];
    if (old('alumnos')) {
        $initialAlumnosData = \App\Models\User::whereIn('id', old('alumnos'))->get()->map(function($alumno) {
            return [
                'id' => $alumno->id,
                'nombre_completo' => $alumno->nombre . ' ' . $alumno->apellido,
                'image' => $alumno->image ? asset('storage/' . $alumno->image) : null,
                'inicial' => substr($alumno->nombre, 0, 1)
            ];
        })->toArray();
    } elseif (request('alumnoId')) {
        $alumno = \App\Models\User::find(request('alumnoId'));
        if ($alumno) {
            $initialAlumnosData[] = [
                'id' => $alumno->id,
                'nombre_completo' => $alumno->nombre . ' ' . $alumno->apellido,
                'image' => $alumno->image ? asset('storage/' . $alumno->image) : null,
                'inicial' => substr($alumno->nombre, 0, 1)
            ];
        }
    }
@endphp
<div class="max-w-4xl mx-auto"
     x-data="pagoEditor({
        selectedAlumnos: {{ json_encode(old('alumnos', request('alumnoId') ? [request('alumnoId')] : [])) }},
        selectedAlumnosData: {{ json_encode($initialAlumnosData) }}
     })">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Registrar Pago</h1>
            <p class="text-gray-600">Registra un nuevo pago de alumno</p>
        </div>
        <a href="{{ route('pagos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('pagos.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2 md:col-span-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-user text-blue-600"></i>
                            Seleccionar Alumno(s)
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
                        <p x-show="selectedAlumnosData.length === 0" class="text-xs text-gray-400 italic w-full text-center py-2">No hay alumnos seleccionados</p>
                    </div>

                    @error('alumnos')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="monto" class="text-sm font-semibold text-gray-700">Monto</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" step="0.01" name="monto" id="monto" value="{{ old('monto') }}" required
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                               placeholder="0.00">
                    </div>
                    @error('monto')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fechaPago" class="text-sm font-semibold text-gray-700">Fecha de Pago</label>
                    <input type="date" name="fechaPago" id="fechaPago" value="{{ old('fechaPago', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fechaPago')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fechaVencimiento" class="text-sm font-semibold text-gray-700">Fecha de Vencimiento</label>
                    <input type="date" name="fechaVencimiento" id="fechaVencimiento" value="{{ old('fechaVencimiento') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fechaVencimiento')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="mesCorrespondiente" class="text-sm font-semibold text-gray-700">Mes Correspondiente</label>
                    <input type="month" name="mesCorrespondiente" id="mesCorrespondiente" value="{{ old('mesCorrespondiente', date('Y-m')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('mesCorrespondiente')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="estado" class="text-sm font-semibold text-gray-700">Estado</label>
                    <select name="estado" id="estado" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ old('estado', 'pagado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="vencido" {{ old('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('estado')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="notas" class="text-sm font-semibold text-gray-700">Notas/Concepto</label>
                <textarea name="notas" id="notas" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                          placeholder="Detalles adicionales del pago...">{{ old('notas') }}</textarea>
                @error('notas')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('pagos.index') }}"
                   class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    <i class="fas fa-save mr-2"></i> Guardar cambios
                </button>
            </div>
        </form>
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
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pagoEditor', (config) => ({
            selectedAlumnos: config.selectedAlumnos,
            selectedAlumnosData: config.selectedAlumnosData,
            isModalOpen: false,
            searchQuery: '',
            results: [],
            loading: false,

            openModal() {
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
                if (!this.selectedAlumnos.includes(alumno.id)) {
                    this.selectedAlumnos.push(alumno.id);
                    this.selectedAlumnosData.push(alumno);
                }
            },

            removeAlumno(id) {
                this.selectedAlumnos = this.selectedAlumnos.filter(aId => aId !== id);
                this.selectedAlumnosData = this.selectedAlumnosData.filter(a => a.id !== id);
            }
        }));
    });
</script>
@endpush
