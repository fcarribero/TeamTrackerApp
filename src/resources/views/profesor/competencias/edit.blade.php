@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('competencias.index') }}" class="bg-white p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Información de Competencia</h1>
            <p class="text-gray-600">Alumno: {{ $competencia->alumno->nombre }} {{ $competencia->alumno->apellido }} | {{ $competencia->nombre }} ({{ $competencia->fecha->format('d/m/Y' . ($competencia->fecha->format('H:i') !== '00:00' ? ' H:i' : '')) }})</p>
        </div>
    </div>

    <div class="max-w-3xl">
        <form action="{{ route('competencias.update', $competencia->id) }}" method="POST" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha -->
                    <div class="space-y-2">
                        <label for="fecha" class="block text-sm font-bold text-gray-700">Fecha y Hora de la Competencia</label>
                        <input type="datetime-local" name="fecha" id="fecha" value="{{ old('fecha', $competencia->fecha->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-red-500"></i>
                        Ubicación
                    </h3>

                    <div>
                        <label for="ubicación" class="block text-xs font-bold text-gray-500 uppercase mb-1">Ciudad, País</label>
                        <input type="text" name="ubicación" id="ubicación" value="{{ old('ubicación', $competencia->ubicación) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej: Buenos Aires, Argentina">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="latitud" class="block text-xs font-bold text-gray-500 uppercase">Latitud</label>
                            <input type="number" step="any" name="latitud" id="latitud" value="{{ old('latitud', $competencia->latitud) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        </div>
                        <div class="space-y-1">
                            <label for="longitud" class="block text-xs font-bold text-gray-500 uppercase">Longitud</label>
                            <input type="number" step="any" name="longitud" id="longitud" value="{{ old('longitud', $competencia->longitud) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tiempo Objetivo -->
                    <div class="space-y-2">
                        <label for="tiempo_objetivo" class="block text-sm font-bold text-gray-700">Tiempo Objetivo</label>
                        <input type="text" name="tiempo_objetivo" id="tiempo_objetivo" value="{{ old('tiempo_objetivo', $competencia->tiempo_objetivo) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej: 03:45:00 o 4:30 min/km">
                        <p class="text-xs text-gray-500">Define la marca o ritmo que el alumno debe buscar.</p>
                    </div>

                    <!-- Resultado Obtenido -->
                    <div class="space-y-2">
                        <label for="resultado_obtenido" class="block text-sm font-bold text-gray-700">Resultado Obtenido</label>
                        <input type="text" name="resultado_obtenido" id="resultado_obtenido" value="{{ old('resultado_obtenido', $competencia->resultado_obtenido) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej: 03:42:15 - ¡Puesto 12!">
                        <p class="text-xs text-gray-500">Completa esto una vez finalizada la competencia.</p>
                    </div>
                </div>

                <!-- Plan de Carrera -->
                <div class="space-y-2">
                    <label for="plan_carrera" class="block text-sm font-bold text-gray-700">Plan de Carrera</label>
                    <textarea name="plan_carrera" id="plan_carrera" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                              placeholder="Describe la estrategia para la carrera...">{{ old('plan_carrera', $competencia->plan_carrera) }}</textarea>
                    <p class="text-xs text-gray-500">Estrategia de ritmos, hidratación, etc.</p>
                </div>

                <!-- Observaciones -->
                <div class="space-y-2">
                    <label for="observaciones" class="block text-sm font-bold text-gray-700">Observaciones adicionales</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                              placeholder="Cualquier otra nota relevante...">{{ old('observaciones', $competencia->observaciones) }}</textarea>
                </div>
            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('competencias.index') }}" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
