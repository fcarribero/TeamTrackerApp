@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Competencias']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Competencias de Alumnos</h1>
            <p class="text-gray-600">Visualiza y completa los planes de carrera de tus alumnos.</p>
        </div>
    </div>

    <!-- Buscador -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('competencias.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="Buscar por competencia, nombre o apellido del alumno...">
            </div>
            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('competencias.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium flex items-center justify-center">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <div id="tour-lista-competencias" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Competencia</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ubicación</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado Info</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($competencias as $competencia)
                        <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                    @if($competencia->alumno->image)
                                        <img src="{{ asset('storage/' . $competencia->alumno->image) }}" alt="{{ $competencia->alumno->nombre }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">
                                            {{ substr($competencia->alumno->nombre, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-gray-900">{{ $competencia->alumno->nombre }} {{ $competencia->alumno->apellido }}</p>
                                        <x-new-user-badge :user="$competencia->alumno" />
                                    </div>
                                </div>
                            </div>
                        </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $competencia->nombre }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ $competencia->ubicación ?: '-' }}</p>
                                @if($competencia->latitud && $competencia->longitud)
                                    @php
                                        $weatherService = app(\App\Services\WeatherService::class);
                                        $esPasada = $competencia->fecha->isPast();
                                        $clima = $esPasada
                                            ? $weatherService->getWeather((float)$competencia->latitud, (float)$competencia->longitud, $competencia->fecha)
                                            : $weatherService->getDailyForecast((float)$competencia->latitud, (float)$competencia->longitud, $competencia->fecha);
                                    @endphp
                                    @if($clima)
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($esPasada)
                                                <span class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded border border-green-100 font-bold"
                                                      title="Clima: {{ $clima->cielo }}">
                                                    <i class="fas fa-{{ $clima->icono ?? 'sun' }} mr-1"></i> {{ $clima->temperatura }}°C
                                                </span>
                                            @else
                                                <span class="text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100 font-bold"
                                                      title="{{ ($clima->is_historical ?? false) ? 'Basado en clima del año pasado' : ($clima->mañana . ' / ' . $clima->tarde . ' / ' . $clima->noche) }}">
                                                    <i class="fas {{ ($clima->is_historical ?? false) ? 'fa-history' : 'fa-cloud-sun' }} mr-1"></i> {{ $clima->min }}°/{{ $clima->max }}°C
                                                    @if($clima->is_historical ?? false)
                                                        <span class="ml-1 text-[8px] opacity-70">*</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">
                                    {{ $competencia->fecha->isoFormat('D [de] MMMM [de] YYYY') }}
                                    @if($competencia->fecha->format('H:i') !== '00:00')
                                        <span class="text-xs font-bold text-blue-600 block">{{ $competencia->fecha->format('H:i') }}</span>
                                    @endif
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if($competencia->plan_carrera && $competencia->tiempo_objetivo)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i> Completo
                                    </span>
                                @else
                                    @if($competencia->fecha->lessThan(now()))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-exclamation-circle"></i> Incompleto (Pasada)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-circle"></i> Pendiente Info
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('competencias.edit', $competencia->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Completar información">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('competencias.destroy', $competencia->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta competencia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-medal text-4xl mb-3 opacity-20"></i>
                                <p>
                                    @if(request('search'))
                                        No se encontraron competencias para "{{ request('search') }}"
                                    @else
                                        No hay competencias registradas por los alumnos.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
