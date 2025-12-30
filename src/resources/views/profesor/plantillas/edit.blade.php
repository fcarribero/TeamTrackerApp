@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Plantillas', 'url' => route('plantillas.index')],
        ['label' => 'Editar Plantilla']
    ]])
@endsection

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

            @php
                $calentamiento = old('contenido.calentamiento', is_array($plantilla->contenido['calentamiento'] ?? null) ? implode("\n", $plantilla->contenido['calentamiento']) : ($plantilla->contenido['calentamiento'] ?? ''));
                $trabajo = old('contenido.trabajo_principal', is_array($plantilla->contenido['trabajo_principal'] ?? null) ? implode("\n", $plantilla->contenido['trabajo_principal']) : ($plantilla->contenido['trabajo_principal'] ?? ''));
                $enfriamiento = old('contenido.enfriamiento', is_array($plantilla->contenido['enfriamiento'] ?? null) ? implode("\n", $plantilla->contenido['enfriamiento']) : ($plantilla->contenido['enfriamiento'] ?? ''));
            @endphp

            <div class="grid grid-cols-1 gap-6">
                <!-- Calentamiento -->
                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <h3 class="text-orange-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-fire"></i> Calentamiento
                    </h3>
                    <textarea name="contenido[calentamiento]" rows="3"
                              class="w-full px-4 py-2 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition text-sm"
                              placeholder="Describe el calentamiento...">{{ $calentamiento }}</textarea>
                </div>

                <!-- Trabajo Principal -->
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <h3 class="text-blue-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-dumbbell"></i> Trabajo Principal
                    </h3>
                    <textarea name="contenido[trabajo_principal]" rows="5"
                              class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm"
                              placeholder="Describe el trabajo principal...">{{ $trabajo }}</textarea>
                </div>

                <!-- Enfriamiento -->
                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                    <h3 class="text-green-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-wind"></i> Enfriamiento
                    </h3>
                    <textarea name="contenido[enfriamiento]" rows="3"
                              class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 outline-none transition text-sm"
                              placeholder="Describe el enfriamiento...">{{ $enfriamiento }}</textarea>
                </div>
            </div>
            @error('contenido')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

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
