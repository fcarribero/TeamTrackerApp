@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('alumno.competencias') }}" class="bg-white p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Competencia</h1>
            <p class="text-gray-600">Modifica los detalles de tu competencia programada.</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('alumno.competencias.update', $competencia->id) }}" method="POST" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <div>
                    <label for="nombre" class="block text-sm font-bold text-gray-700 mb-1">Nombre de la Competencia</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $competencia->nombre) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                           placeholder="Ej: Maratón de Buenos Aires">
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-bold text-gray-700 mb-1">Fecha y Hora</label>
                    <input type="datetime-local" name="fecha" id="fecha" value="{{ old('fecha', $competencia->fecha->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-red-500"></i>
                        Ubicación de la Competencia
                    </h3>

                    <div>
                        <label for="ubicación" class="block text-xs font-bold text-gray-500 uppercase mb-1">Ciudad, País</label>
                        <input type="text" name="ubicación" id="ubicación" value="{{ old('ubicación', $competencia->ubicación) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej: Buenos Aires, Argentina">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="latitud" class="block text-xs font-bold text-gray-500 uppercase mb-1">Latitud</label>
                            <input type="number" step="any" name="latitud" id="latitud" value="{{ old('latitud', $competencia->latitud) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label for="longitud" class="block text-xs font-bold text-gray-500 uppercase mb-1">Longitud</label>
                            <input type="number" step="any" name="longitud" id="longitud" value="{{ old('longitud', $competencia->longitud) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        </div>
                    </div>

                    <button type="button" onclick="getLocation()" class="w-full text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 justify-center py-1">
                        <i class="fas fa-location-arrow"></i> Actualizar con mi ubicación actual
                    </button>
                </div>

                <hr class="border-gray-100">

                <div>
                    <label for="resultado_obtenido" class="block text-sm font-bold text-gray-700 mb-1">Resultado Obtenido</label>
                    <textarea name="resultado_obtenido" id="resultado_obtenido" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                              placeholder="Ej: 1:45:30 - Muy buenas sensaciones, mantuve el ritmo planeado...">{{ old('resultado_obtenido', $competencia->resultado_obtenido) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 italic">Completa esto una vez finalizada la competencia.</p>
                </div>
            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('alumno.competencias') }}" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">
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

@push('scripts')
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitud').value = position.coords.latitude;
            document.getElementById('longitud').value = position.coords.longitude;
        }, function(error) {
            alert('Error al obtener la ubicación: ' + error.message);
        });
    } else {
        alert("La geolocalización no es compatible con este navegador.");
    }
}
</script>
@endpush
