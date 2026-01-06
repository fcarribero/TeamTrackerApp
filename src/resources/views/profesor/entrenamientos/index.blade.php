@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Entrenamientos']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Entrenamientos</h1>
            <p class="text-gray-600">Calendario y asignación de sesiones</p>
        </div>
        <a href="{{ route('entrenamientos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Asignar Entrenamiento
        </a>
    </div>

    <div id="tour-lista-entrenamientos" class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <div class="space-y-4">
            @forelse($entrenamientos as $entrenamiento)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="bg-blue-500 p-3 rounded-lg text-white">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">{{ $entrenamiento->titulo }}</h3>
                        <div class="flex items-center gap-4">
                            <div class="flex -space-x-2 overflow-hidden">
                                @foreach($entrenamiento->all_alumnos->take(5) as $alumno)
                                    <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs overflow-hidden shrink-0" title="{{ $alumno->nombre }} {{ $alumno->apellido }}">
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
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($entrenamiento->fecha)->isoFormat('D [de] MMMM') }}
                                @if($entrenamiento->distanciaTotal)
                                    • <span class="text-blue-600 font-semibold">{{ $entrenamiento->distanciaTotal }} km</span>
                                @endif
                                @if($entrenamiento->tiempoTotal)
                                    • <span class="text-blue-600 font-semibold">{{ $entrenamiento->tiempoTotal }} min</span>
                                @endif
                            </p>
                        </div>
                        @if($entrenamiento->resultados_count > 0)
                            <a href="{{ route('entrenamientos.show', $entrenamiento->id) }}" class="inline-flex items-center gap-1 mt-1 text-xs font-bold text-blue-600 hover:underline">
                                <i class="fas fa-comment-dots"></i>
                                {{ $entrenamiento->resultados_count }} {{ $entrenamiento->resultados_count == 1 ? 'devolución' : 'devoluciones' }}
                            </a>
                        @else
                            <p class="text-[10px] text-gray-400 mt-1 italic">Sin devoluciones aún</p>
                        @endif

                        @if(isset($profesor) && $profesor->latitud)
                            @php
                                $clima = app(\App\Services\WeatherService::class)->getDailyForecast((float)$profesor->latitud, (float)$profesor->longitud, \Carbon\Carbon::parse($entrenamiento->fecha));
                            @endphp
                            @if($clima)
                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 items-center text-[10px] border-t border-gray-100 pt-2">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-bold border border-blue-100" title="{{ ($clima->is_historical ?? false) ? 'Referencia histórica basada en el año pasado' : '' }}">
                                        <i class="fas {{ ($clima->is_historical ?? false) ? 'fa-history' : 'fa-temperature-low' }} mr-1 opacity-70"></i>{{ $clima->min }}° /
                                        <i class="fas fa-temperature-high mr-1 opacity-70"></i>{{ $clima->max }}°C
                                        @if($clima->is_historical ?? false) * @endif
                                    </span>
                                    @if(!($clima->is_historical ?? false))
                                        <div class="flex items-center gap-3 text-gray-500">
                                            <span class="flex items-center gap-1"><i class="fas fa-sun text-yellow-500 w-3 text-center"></i> Mañana: <b class="text-gray-700">{{ $clima->mañana }}</b></span>
                                            <span class="flex items-center gap-1"><i class="fas fa-cloud-sun text-orange-400 w-3 text-center"></i> Tarde: <b class="text-gray-700">{{ $clima->tarde }}</b></span>
                                            <span class="flex items-center gap-1"><i class="fas fa-moon text-blue-400 w-3 text-center"></i> Noche: <b class="text-gray-700">{{ $clima->noche }}</b></span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Clima histórico de referencia</span>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('entrenamientos.edit', $entrenamiento->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('entrenamientos.destroy', $entrenamiento->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este entrenamiento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">No hay entrenamientos programados</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
