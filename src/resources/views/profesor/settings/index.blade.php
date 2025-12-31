@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Configuración']]])
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-cog text-blue-500"></i>
            Configuración General
        </h1>
        <p class="text-gray-600 mt-1">Personaliza la identidad de tu equipo y otros ajustes globales.</p>
    </div>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-600"></i>
                        Perfil del Profesor
                    </h3>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Tu Nombre</label>
                        <p class="text-xs text-gray-500 mb-3">Este es el nombre que se mostrará en tu perfil y en la cabecera del panel.</p>

                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white"
                               placeholder="Tu nombre completo" required>

                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-id-card text-blue-600"></i>
                        Identidad del Equipo
                    </h3>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 space-y-4">
                        <div>
                            <label for="team_name" class="block text-sm font-bold text-blue-900 mb-1">Nombre del Grupo / Equipo</label>
                            <p class="text-xs text-blue-700 mb-3">Este nombre aparecerá destacado en el panel de los alumnos y en las comunicaciones oficiales.</p>

                            <input type="text" name="team_name" id="team_name" value="{{ old('team_name', $teamName) }}"
                                   class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white"
                                   placeholder="Ej: Runners Elite, Team Pro, etc.">

                            @error('team_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <label for="team_logo" class="block text-sm font-bold text-blue-900 mb-1">Logo del Equipo</label>
                            <p class="text-xs text-blue-700 mb-3">Sube una imagen para personalizar la cabecera del panel (JPG, PNG, max 2MB).</p>

                            <div class="flex items-center gap-4">
                                @if($teamLogo)
                                    <div class="w-16 h-16 rounded-xl border border-blue-200 bg-white p-1 shadow-sm overflow-hidden flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $teamLogo) }}" alt="Logo actual" class="max-w-full max-h-full object-contain">
                                    </div>
                                @endif
                                <input type="file" name="team_logo" id="team_logo"
                                       class="block w-full text-sm text-blue-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition">
                            </div>

                            @error('team_logo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                        Ubicación de Referencia
                    </h3>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-4">
                        <p class="text-xs text-gray-500">Esta ubicación se utilizará como referencia para mostrar estimaciones climáticas en los entrenamientos futuros.</p>

                        <div>
                            <label for="ciudad" class="block text-sm font-bold text-gray-700 mb-1">Ciudad / Referencia</label>
                            <input type="text" name="ciudad" id="ciudad" value="{{ old('ciudad', $user->ciudad) }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white"
                                   placeholder="Ej: Buenos Aires, AR">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="latitud" class="block text-sm font-bold text-gray-700 mb-1">Latitud</label>
                                <input type="number" step="any" name="latitud" id="latitud" value="{{ old('latitud', $user->latitud) }}"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                            </div>
                            <div>
                                <label for="longitud" class="block text-sm font-bold text-gray-700 mb-1">Longitud</label>
                                <input type="number" step="any" name="longitud" id="longitud" value="{{ old('longitud', $user->longitud) }}"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                            </div>
                        </div>

                        <button type="button" onclick="getLocation()" class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <i class="fas fa-location-arrow"></i> Usar mi ubicación actual
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 font-bold shadow-lg shadow-blue-200">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
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
