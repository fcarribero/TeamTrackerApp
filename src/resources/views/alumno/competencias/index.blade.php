@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Competencias</h1>
            <p class="text-gray-600">Registra tus próximas carreras y consulta los planes del profesor.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulario de Carga -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 sticky top-24">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-500"></i>
                    Nueva Competencia
                </h2>
                <form action="{{ route('alumno.competencias.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Competencia</label>
                        <input type="text" name="nombre" id="nombre" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej: Maratón de Buenos Aires">
                    </div>

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora</label>
                        <input type="datetime-local" name="fecha" id="fecha" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="ubicación" class="block text-sm font-medium text-gray-700 mb-1">Ubicación (Ciudad, País)</label>
                        <input type="text" name="ubicación" id="ubicación"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Ej: Buenos Aires, Argentina">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="latitud" class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                            <input type="number" step="any" name="latitud" id="latitud"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label for="longitud" class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                            <input type="number" step="any" name="longitud" id="longitud"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        </div>
                    </div>

                    <button type="button" onclick="getLocation()" class="w-full text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 justify-center py-1">
                        <i class="fas fa-location-arrow"></i> Usar mi ubicación actual
                    </button>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        Cargar Competencia
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Competencias -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-xl font-bold text-gray-900 px-1">Mis Inscripciones</h2>

            @forelse($competencias as $competencia)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                                    <i class="fas fa-medal text-xl"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $competencia->nombre }}</h3>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('alumno.competencias.edit', $competencia->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <form action="{{ route('alumno.competencias.destroy', $competencia->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta competencia?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 mt-1">
                                        <p class="flex items-center gap-1">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ $competencia->fecha->format('d/m/Y' . ($competencia->fecha->format('H:i') !== '00:00' ? ' H:i' : '')) }}
                                        </p>
                                        @if($competencia->ubicación)
                                            <p class="flex items-center gap-1">
                                                <i class="fas fa-map-marker-alt text-red-400"></i>
                                                {{ $competencia->ubicación }}
                                            </p>
                                        @endif
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $competencia->fecha->isPast() ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700' }}">
                                            {{ $competencia->fecha->isPast() ? 'Finalizada' : 'Próxima' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($competencia->latitud && $competencia->longitud)
                                @php
                                    $clima = app(\App\Services\WeatherService::class)->getDailyForecast((float)$competencia->latitud, (float)$competencia->longitud, $competencia->fecha);
                                @endphp
                                @if($clima)
                                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2 flex items-center gap-4 shadow-sm relative group">
                                        <div class="text-center {{ !($clima->is_historical ?? false) ? 'border-r border-blue-200 pr-4' : '' }}">
                                            <p class="text-[10px] uppercase font-black text-blue-400">Temp</p>
                                            <p class="text-sm font-bold text-blue-700">{{ $clima->min }}°/{{ $clima->max }}°C</p>
                                        </div>
                                        @if(!($clima->is_historical ?? false))
                                            <div class="flex items-center gap-3">
                                                <div class="text-center">
                                                    <p class="text-[9px] uppercase font-bold text-gray-400">Mañana</p>
                                                    <i class="fas fa-sun text-yellow-500 text-xs" title="{{ $clima->mañana }}"></i>
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-[9px] uppercase font-bold text-gray-400">Tarde</p>
                                                    <i class="fas fa-cloud-sun text-orange-400 text-xs" title="{{ $clima->tarde }}"></i>
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-[9px] uppercase font-bold text-gray-400">Noche</p>
                                                    <i class="fas fa-moon text-blue-400 text-xs" title="{{ $clima->noche }}"></i>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-[9px] text-blue-400 italic leading-tight max-w-[120px]">
                                                <i class="fas fa-info-circle mr-1"></i> Basado en el clima del año pasado
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>

                        @if(!$competencia->resultado_obtenido)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Plan de Carrera</p>
                                <p class="text-gray-700 italic">
                                    {{ $competencia->plan_carrera ?: 'Pendiente de completar por el profesor...' }}
                                </p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <p class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-2">Tiempo Objetivo</p>
                                <p class="text-xl font-bold text-blue-900">
                                    {{ $competencia->tiempo_objetivo ?: '--:--' }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($competencia->observaciones)
                            <div class="mt-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                                <p class="text-xs font-bold text-yellow-600 uppercase tracking-wider mb-1">Observaciones</p>
                                <p class="text-gray-700">{{ $competencia->observaciones }}</p>
                            </div>
                        @endif

                        @if($competencia->resultado_obtenido)
                            <div class="mt-4 p-4 bg-green-50 rounded-xl border border-green-100">
                                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Resultado Obtenido</p>
                                <p class="text-gray-800 font-semibold">{{ $competencia->resultado_obtenido }}</p>
                            </div>
                        @elseif($competencia->fecha->isPast())
                            <div class="mt-4">
                                <a href="{{ route('alumno.competencias.edit', $competencia->id) }}" class="flex items-center justify-center gap-2 w-full py-3 bg-green-100 text-green-700 font-bold rounded-xl border border-green-200 hover:bg-green-200 transition">
                                    <i class="fas fa-trophy"></i>
                                    Cargar Resultado de la Competencia
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-12 text-center border border-dashed border-gray-300">
                    <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-running text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">No tienes competencias cargadas</h3>
                    <p class="text-gray-500">Registra tu próxima competencia para que tu profesor pueda ayudarte con el plan.</p>
                </div>
            @endforelse
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
