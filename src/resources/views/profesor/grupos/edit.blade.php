@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Grupos', 'url' => route('grupos.index')],
        ['label' => 'Editar Grupo']
    ]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Grupo</h1>
            <p class="text-gray-600">Actualiza la información de {{ $grupo->nombre }}</p>
        </div>
        <a href="{{ route('grupos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('grupos.update', $grupo->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="nombre" class="text-sm font-semibold text-gray-700">Nombre del Grupo</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $grupo->nombre) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Ej. Avanzados Mañana">
                    @error('nombre')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="color" class="text-sm font-semibold text-gray-700">Color Distintivo</label>
                    <div class="flex gap-2">
                        <input type="color" name="color" id="color" value="{{ old('color', $grupo->color ?? '#3B82F6') }}"
                               class="h-10 w-20 p-1 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" value="{{ old('color', $grupo->color ?? '#3B82F6') }}" readonly
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 outline-none">
                    </div>
                    @error('color')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="descripcion" class="text-sm font-semibold text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                          placeholder="Propósito del grupo, horarios, etc...">{{ old('descripcion', $grupo->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-4">
                <label class="text-sm font-semibold text-gray-700 block">Asignar Alumnos</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200 max-h-60 overflow-y-auto">
                    @php $currentAlumnos = $grupo->alumnos->pluck('id')->toArray(); @endphp
                    @forelse($alumnos as $alumno)
                        <label class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-100 hover:border-blue-300 transition cursor-pointer">
                            <input type="checkbox" name="alumnos[]" value="{{ $alumno->id }}"
                                   {{ in_array($alumno->id, old('alumnos', $currentAlumnos)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-900">{{ $alumno->nombre }}</span>
                        </label>
                    @empty
                        <p class="col-span-full text-center text-gray-500 py-4 italic text-sm">No hay alumnos registrados</p>
                    @endforelse
                </div>
                @error('alumnos')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('grupos.index') }}"
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
