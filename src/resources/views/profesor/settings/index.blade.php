@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Configuración']]])
@endsection

@section('content')
<div class="space-y-6" x-data="{ tab: 'general' }">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-cog text-blue-500"></i>
            Configuración
        </h1>
        <p class="text-gray-600 mt-1">Administra tus preferencias personales y del equipo.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar de Pestañas -->
        <div class="w-full md:w-64 space-y-2">
            <button @click="tab = 'general'"
                    :class="tab === 'general' ? 'bg-blue-600 text-white shadow-blue-200' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-4 py-3 rounded-xl font-bold transition flex items-center gap-3 shadow-sm border border-gray-100">
                <i class="fas fa-user-cog" :class="tab === 'general' ? 'text-white' : 'text-blue-500'"></i>
                General
            </button>
            <button @click="tab = 'notifications'"
                    :class="tab === 'notifications' ? 'bg-blue-600 text-white shadow-blue-200' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-4 py-3 rounded-xl font-bold transition flex items-center gap-3 shadow-sm border border-gray-100">
                <i class="fas fa-bell" :class="tab === 'notifications' ? 'text-white' : 'text-blue-500'"></i>
                Notificaciones
            </button>
        </div>

        <!-- Contenido -->
        <div class="flex-1">
            <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tab: General -->
                <div x-show="tab === 'general'" class="space-y-6" x-cloak>
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden p-6 space-y-8">
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-user-circle text-blue-600"></i>
                                Perfil del Profesor
                            </h3>

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre" class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $user->nombre) }}"
                                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white"
                                           placeholder="Tu nombre" required>
                                    @error('nombre')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="apellido" class="block text-sm font-bold text-gray-700 mb-1">Apellido</label>
                                    <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $user->apellido) }}"
                                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white"
                                           placeholder="Tu apellido">
                                    @error('apellido')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
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

                                <button type="button" onclick="getLocation()" class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 font-semibold">
                                    <i class="fas fa-location-arrow"></i> Usar mi ubicación actual
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Notificaciones -->
                <div x-show="tab === 'notifications'" class="space-y-6" x-cloak>
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 space-y-6">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Preferencias de Notificación</h3>
                                <p class="text-sm text-gray-500">Controla cuándo y cómo quieres recibir alertas.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 group hover:border-blue-200 transition-colors">
                                <div class="pr-4">
                                    <p class="font-bold text-gray-800">Aceptación de Invitación</p>
                                    <p class="text-sm text-gray-500">Recibir un correo electrónico cada vez que un nuevo alumno acepte una invitación para unirse a tu equipo.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="notify_invitation_accepted" value="0">
                                    <input type="checkbox" name="notify_invitation_accepted" value="1"
                                           {{ \App\Models\Setting::get('notify_invitation_accepted', '1') === '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition flex items-center gap-2 font-bold shadow-lg shadow-blue-200 hover:scale-105 transform active:scale-95">
                        <i class="fas fa-save"></i>
                        Guardar Configuración
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
