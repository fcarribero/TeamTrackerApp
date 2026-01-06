@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs')
@endsection

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard de Profesor</h1>
        <p class="text-gray-600">Resumen general de tus alumnos y entrenamientos</p>
    </div>

    <!-- Stats Cards -->
    <div id="tour-stats-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                    <i class="fas fa-users fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Alumnos</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $totalAlumnos }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-100 p-3 rounded-lg text-yellow-600">
                    <i class="fas fa-credit-card fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Pagos Pendientes</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $pagosPendientes }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-lg text-green-600">
                    <i class="fas fa-dollar-sign fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Ingresos del Mes</h3>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($ingresosMesActual, 2) }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 p-3 rounded-lg text-purple-600">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Próximos Entrenamientos</h3>
            <p class="text-3xl font-bold text-gray-900">{{ count($proximosEntrenamientos) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Proximos Entrenamientos -->
        <div id="tour-proximos-entrenamientos" class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clock text-blue-500"></i>
                    Próximos Entrenamientos
                </h2>
                <a href="/dashboard/profesor/entrenamientos" class="text-blue-500 hover:text-blue-600 text-sm font-medium">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($proximosEntrenamientos as $entrenamiento)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location='{{ route('entrenamientos.edit', $entrenamiento->id) }}'">
                        <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $entrenamiento->titulo }}</p>
                            <div class="flex items-center gap-4 mt-1">
                                <div class="flex -space-x-2 overflow-hidden">
                                    @foreach($entrenamiento->all_alumnos->take(5) as $alumno)
                                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs overflow-hidden shrink-0" title="{{ $alumno->name }}">
                                            @if($alumno->image)
                                                <img src="{{ asset('storage/' . $alumno->image) }}" alt="{{ $alumno->nombre }}" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($alumno->nombre, 0, 1) }}
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($entrenamiento->all_alumnos->count() > 5)
                                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs">
                                            +{{ $entrenamiento->all_alumnos->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                                @if($entrenamiento->grupos->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($entrenamiento->grupos as $grupo)
                                            <x-group-tag :grupo="$grupo" />
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @if(isset($profesor) && $profesor->latitud)
                                @php
                                    $clima = app(\App\Services\WeatherService::class)->getDailyForecast((float)$profesor->latitud, (float)$profesor->longitud, \Carbon\Carbon::parse($entrenamiento->fecha));
                                @endphp
                                @if($clima)
                                    <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 items-center text-[10px] border-t border-gray-100 pt-2">
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-bold border border-blue-100" title="{{ ($clima->is_historical ?? false) ? 'Referencia histórica basada en el año pasado' : '' }}">
                                            <i class="fas {{ ($clima->is_historical ?? false) ? 'fa-history' : 'fa-temperature-low' }} mr-1 opacity-70"></i>{{ $clima->min }}° /
                                            <i class="fas fa-temperature-high mr-1 opacity-70"></i>{{ $clima->max }}°C
                                            @if($clima->is_historical ?? false) * @endif
                                        </span>
                                        @if(!($clima->is_historical ?? false))
                                            <div class="flex items-center gap-2 text-gray-500">
                                                <span class="flex items-center gap-1"><i class="fas fa-sun text-yellow-500 w-3 text-center"></i> Mañana: <b class="text-gray-700">{{ $clima->mañana }}</b></span>
                                                <span class="flex items-center gap-1"><i class="fas fa-cloud-sun text-orange-400 w-3 text-center"></i> Tarde: <b class="text-gray-700">{{ $clima->tarde }}</b></span>
                                                <span class="flex items-center gap-1"><i class="fas fa-moon text-blue-400 w-3 text-center"></i> Noche: <b class="text-gray-700">{{ $clima->noche }}</b></span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Clima histórico</span>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($entrenamiento->fecha)->format('d M') }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($entrenamiento->fecha)->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No hay entrenamientos programados</p>
                @endforelse
            </div>
        </div>

        <!-- Ultimos Alumnos -->
        <div id="tour-ultimos-alumnos" class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-users text-blue-500"></i>
                    Últimos Alumnos
                </h2>
                <a href="/dashboard/profesor/alumnos" class="text-blue-500 hover:text-blue-600 text-sm font-medium">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($ultimosAlumnos as $alumno)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location='{{ route('alumnos.show', $alumno->id) }}'">
                        @if($alumno->image)
                            <img src="{{ asset('storage/' . $alumno->image) }}" alt="{{ $alumno->nombre }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="bg-green-100 p-2 rounded-full text-green-600 w-10 h-10 flex items-center justify-center">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900">{{ $alumno->nombre }} {{ $alumno->apellido }}</p>
                                <x-new-user-badge :user="$alumno" />
                            </div>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($alumno->grupos as $grupo)
                                    <x-group-tag :grupo="$grupo" />
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($alumno->created_at)->format('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No hay alumnos registrados</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
