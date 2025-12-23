@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-blue-500"></i>
            Mis Entrenamientos
        </h1>
        <p class="text-gray-600 mt-1">Revisa tus entrenamientos programados</p>
    </div>

    @php
        $hoy = \Carbon\Carbon::today();
        $proximos = $entrenamientos->filter(fn($e) => \Carbon\Carbon::parse($e->fecha)->startOfDay() >= $hoy)->sortBy('fecha');
        $pasados = $entrenamientos->filter(fn($e) => \Carbon\Carbon::parse($e->fecha)->startOfDay() < $hoy)->sortByDesc('fecha');
    @endphp

    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-clock text-blue-500"></i>
            Próximos Entrenamientos ({{ count($proximos) }})
        </h2>

        <div class="space-y-4">
            @forelse($proximos as $entrenamiento)
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-500 p-3 rounded-lg text-white shadow-md">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $entrenamiento->titulo }}</h3>
                            <p class="text-gray-700 mb-3">
                                {{ \Carbon\Carbon::parse($entrenamiento->fecha)->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') }}
                            </p>

                            @if($entrenamiento->observaciones)
                                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4 shadow-sm rounded-r-lg">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fas fa-exclamation-circle text-amber-600"></i>
                                        <span class="font-bold text-amber-900 text-sm">Observaciones del Profesor:</span>
                                    </div>
                                    <p class="text-amber-800 text-sm italic">{{ $entrenamiento->observaciones }}</p>
                                </div>
                            @endif

                            @if($entrenamiento->plantilla)
                                <div class="bg-white rounded-lg p-4 mb-3 border border-blue-100 shadow-sm">
                                    <div class="flex items-center gap-2 mb-3 border-b border-blue-50 pb-2">
                                        <i class="fas fa-book-open text-blue-600"></i>
                                        <span class="font-bold text-gray-900">{{ $entrenamiento->plantilla->nombre }}</span>
                                    </div>
                                    @if(is_array($entrenamiento->plantilla->contenido))
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <h4 class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Calentamiento</h4>
                                                <div class="space-y-1">
                                                    @forelse($entrenamiento->plantilla->contenido['calentamiento'] ?? [] as $ej)
                                                        <div class="text-xs bg-orange-50 text-orange-800 p-2 rounded-lg border border-orange-100">{{ $ej }}</div>
                                                    @empty
                                                        <div class="text-[10px] text-gray-400 italic">Sin ejercicios</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2">Trabajo Principal</h4>
                                                <div class="space-y-1">
                                                    @forelse($entrenamiento->plantilla->contenido['trabajo_principal'] ?? [] as $ej)
                                                        <div class="text-xs bg-blue-50 text-blue-800 p-2 rounded-lg border border-blue-100">{{ $ej }}</div>
                                                    @empty
                                                        <div class="text-[10px] text-gray-400 italic">Sin ejercicios</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-2">Enfriamiento</h4>
                                                <div class="space-y-1">
                                                    @forelse($entrenamiento->plantilla->contenido['enfriamiento'] ?? [] as $ej)
                                                        <div class="text-xs bg-green-50 text-green-800 p-2 rounded-lg border border-green-100">{{ $ej }}</div>
                                                    @empty
                                                        <div class="text-[10px] text-gray-400 italic">Sin ejercicios</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $entrenamiento->plantilla->contenido }}</div>
                                    @endif
                                </div>
                            @endif

                            @if($entrenamiento->contenidoPersonalizado)
                                <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm">
                                    <div class="flex items-center gap-2 mb-3 border-b border-blue-50 pb-2">
                                        <i class="fas fa-user-edit text-blue-600"></i>
                                        <span class="font-bold text-gray-900">Personalizado / Instrucciones</span>
                                    </div>
                                    @if(is_array($entrenamiento->contenidoPersonalizado))
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <h4 class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Calentamiento</h4>
                                                <div class="space-y-1">
                                                    @forelse($entrenamiento->contenidoPersonalizado['calentamiento'] ?? [] as $ej)
                                                        <div class="text-xs bg-orange-50 text-orange-800 p-2 rounded-lg border border-orange-100">{{ $ej }}</div>
                                                    @empty
                                                        <div class="text-[10px] text-gray-400 italic">Sin ejercicios</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2">Trabajo Principal</h4>
                                                <div class="space-y-1">
                                                    @forelse($entrenamiento->contenidoPersonalizado['trabajo_principal'] ?? [] as $ej)
                                                        <div class="text-xs bg-blue-50 text-blue-800 p-2 rounded-lg border border-blue-100">{{ $ej }}</div>
                                                    @empty
                                                        <div class="text-[10px] text-gray-400 italic">Sin ejercicios</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-2">Enfriamiento</h4>
                                                <div class="space-y-1">
                                                    @forelse($entrenamiento->contenidoPersonalizado['enfriamiento'] ?? [] as $ej)
                                                        <div class="text-xs bg-green-50 text-green-800 p-2 rounded-lg border border-green-100">{{ $ej }}</div>
                                                    @empty
                                                        <div class="text-[10px] text-gray-400 italic">Sin ejercicios</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $entrenamiento->contenidoPersonalizado }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No tienes entrenamientos próximos programados</p>
            @endforelse
        </div>
    </div>

    @if(count($pasados) > 0)
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Entrenamientos Completados ({{ count($pasados) }})</h2>
            <div class="space-y-3">
                @foreach($pasados->take(10) as $entrenamiento)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="bg-gray-300 p-2 rounded-lg text-gray-600">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $entrenamiento->titulo }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($entrenamiento->fecha)->isoFormat('D MMM YYYY [a las] HH:mm') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
