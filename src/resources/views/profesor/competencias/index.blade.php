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

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
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
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                        {{ substr($competencia->alumno->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $competencia->alumno->nombre }} {{ $competencia->alumno->apellido }}</p>
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
                                    {{ $competencia->fecha->format('d/m/Y') }}
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-circle"></i> Pendiente Info
                                    </span>
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
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-medal text-4xl mb-3 opacity-20"></i>
                                <p>No hay competencias registradas por los alumnos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
