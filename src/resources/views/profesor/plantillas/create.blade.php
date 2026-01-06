@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Plantillas', 'url' => route('plantillas.index')],
        ['label' => 'Nueva Plantilla']
    ]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nueva Plantilla</h1>
            <p class="text-gray-600">Crea una plantilla de entrenamiento reutilizable</p>
        </div>
        <a href="{{ route('plantillas.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('plantillas.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="nombre" class="text-sm font-semibold text-gray-700">Nombre de la Plantilla</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                       placeholder="Ej. Rutina Fuerza A">
                @error('nombre')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="descripcion" class="text-sm font-semibold text-gray-700">Descripción Corta</label>
                <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion') }}"
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
                          placeholder="Ej. Mantener una hidratación constante durante la sesión...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Calentamiento -->
                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <h3 class="text-orange-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-fire"></i> Calentamiento
                    </h3>
                    <textarea name="contenido[calentamiento]" rows="3"
                              class="w-full px-4 py-2 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition text-sm"
                              placeholder="Describe el calentamiento...">{{ old('contenido.calentamiento', "20' CCL") }}</textarea>
                </div>

                <!-- Trabajo Principal -->
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <h3 class="text-blue-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-dumbbell"></i> Trabajo Principal
                    </h3>
                    <textarea name="contenido[trabajo_principal]" rows="5"
                              class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm"
                              placeholder="Describe el trabajo principal...">{{ old('contenido.trabajo_principal') }}</textarea>
                </div>

                <!-- Enfriamiento -->
                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                    <h3 class="text-green-800 font-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-wind"></i> Enfriamiento
                    </h3>
                    <textarea name="contenido[enfriamiento]" rows="3"
                              class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 outline-none transition text-sm"
                              placeholder="Describe el enfriamiento...">{{ old('contenido.enfriamiento', "10' CCL") }}</textarea>
                </div>
            </div>
            @error('contenido')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

            <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('plantillas.index') }}"
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
</div>
@endsection
