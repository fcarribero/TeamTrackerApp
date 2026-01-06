@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Entrenamientos', 'url' => route('entrenamientos.index')],
        ['label' => 'Detalle']
    ]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('entrenamientos.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Resultados: {{ $entrenamiento->titulo }}</h1>
                <p class="text-gray-600">{{ \Carbon\Carbon::parse($entrenamiento->fecha)->isoFormat('D [de] MMMM') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
             <a href="{{ route('entrenamientos.edit', $entrenamiento->id) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Editar Entrenamiento
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Resumen del Entrenamiento -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Detalles de la Sesión</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Asignado a</p>
                        <div class="mt-1 flex flex-wrap gap-2">
                            @foreach($entrenamiento->alumnos as $alumno)
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-[10px] font-medium border border-blue-100">
                                    {{ $alumno->nombre }} {{ $alumno->apellido }}
                                </span>
                            @endforeach
                            @foreach($entrenamiento->grupos as $grupo)
                                <x-group-tag :grupo="$grupo" />
                            @endforeach
                        </div>
                    </div>

                    @if($entrenamiento->distanciaTotal || $entrenamiento->tiempoTotal)
                    <div class="flex gap-4 border-t border-b py-3 my-2">
                        @if($entrenamiento->distanciaTotal)
                        <div class="text-center flex-1 border-r border-gray-100">
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Distancia Est.</p>
                            <p class="text-lg font-bold text-blue-600">{{ $entrenamiento->distanciaTotal }} <span class="text-xs text-gray-400">km</span></p>
                        </div>
                        @endif
                        @if($entrenamiento->tiempoTotal)
                        <div class="text-center flex-1">
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Tiempo Est.</p>
                            <p class="text-lg font-bold text-blue-600">{{ $entrenamiento->tiempoTotal }} <span class="text-xs text-gray-400">min</span></p>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($entrenamiento->observaciones)
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Observaciones enviadas</p>
                        <p class="text-sm text-gray-700 italic mt-1">{{ $entrenamiento->observaciones }}</p>
                    </div>
                    @endif

                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Contenido</p>
                            <div class="space-y-2">
                                @if(!empty($entrenamiento->contenidoPersonalizado['calentamiento']))
                                    <p class="text-[10px] font-bold text-orange-500 uppercase">Calentamiento</p>
                                    <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ is_array($entrenamiento->contenidoPersonalizado['calentamiento']) ? implode("\n", $entrenamiento->contenidoPersonalizado['calentamiento']) : $entrenamiento->contenidoPersonalizado['calentamiento'] }}</p>
                                @endif
                                @if(!empty($entrenamiento->contenidoPersonalizado['trabajo_principal']))
                                    <p class="text-[10px] font-bold text-blue-500 uppercase">Trabajo Principal</p>
                                    <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ is_array($entrenamiento->contenidoPersonalizado['trabajo_principal']) ? implode("\n", $entrenamiento->contenidoPersonalizado['trabajo_principal']) : $entrenamiento->contenidoPersonalizado['trabajo_principal'] }}</p>
                                @endif
                                @if(!empty($entrenamiento->contenidoPersonalizado['enfriamiento']))
                                    <p class="text-[10px] font-bold text-green-500 uppercase">Enfriamiento</p>
                                    <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ is_array($entrenamiento->contenidoPersonalizado['enfriamiento']) ? implode("\n", $entrenamiento->contenidoPersonalizado['enfriamiento']) : $entrenamiento->contenidoPersonalizado['enfriamiento'] }}</p>
                                @endif
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <!-- Lista de Resultados -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-poll text-blue-500"></i>
                    Devoluciones de Alumnos ({{ $entrenamiento->resultados->count() }})
                </h3>

                <div class="space-y-6">
                    @forelse($entrenamiento->resultados as $resultado)
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 shadow-sm transition hover:shadow-md">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold overflow-hidden shrink-0 border border-gray-100">
                                        @if($resultado->alumno->image)
                                            <img src="{{ asset('storage/' . $resultado->alumno->image) }}" alt="{{ $resultado->alumno->nombre }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($resultado->alumno->nombre, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-gray-900">{{ $resultado->alumno->nombre }} {{ $resultado->alumno->apellido }}</p>
                                            <x-new-user-badge :user="$resultado->alumno" />
                                        </div>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Completado el {{ $resultado->fecha_realizado ? $resultado->fecha_realizado->isoFormat('D [de] MMMM H:i') : $resultado->updated_at->isoFormat('D [de] MMMM H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php $clima = $resultado->weather(); @endphp
                                    @if($clima)
                                        <div class="flex items-center gap-2 mb-2 bg-blue-50 px-2 py-1 rounded text-xs">
                                            <span title="Temperatura"><i class="fas fa-temperature-high text-orange-400"></i> {{ $clima->temperatura }}°C</span>
                                            <span title="Humedad"><i class="fas fa-tint text-blue-400"></i> {{ $clima->humedad }}%</span>
                                            <span class="font-medium text-blue-600">{{ $clima->cielo }}</span>
                                        </div>
                                    @endif
                                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Dificultad</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full {{ $resultado->dificultad <= 3 ? 'bg-green-500' : ($resultado->dificultad <= 6 ? 'bg-yellow-500' : ($resultado->dificultad <= 8 ? 'bg-orange-500' : 'bg-red-500')) }}"
                                                 style="width: {{ $resultado->dificultad * 10 }}%"></div>
                                        </div>
                                        <span class="text-sm font-black text-gray-700">{{ $resultado->dificultad }}/10</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($resultado->sensacion)
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-gray-400 mb-1 flex items-center gap-1">
                                            <i class="fas fa-smile"></i> Cómo se sintió
                                        </h4>
                                        <p class="text-sm text-gray-800 bg-white p-3 rounded-lg border border-gray-100">{{ $resultado->sensacion }}</p>
                                    </div>
                                @endif

                                @if($resultado->molestias)
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-gray-400 mb-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i> Molestias
                                        </h4>
                                        <p class="text-sm text-red-800 bg-red-50 p-3 rounded-lg border border-red-100">{{ $resultado->molestias }}</p>
                                    </div>
                                @endif

                                @if($resultado->comentarios)
                                    <div class="md:col-span-2">
                                        <h4 class="text-[10px] uppercase font-black text-gray-400 mb-1 flex items-center gap-1">
                                            <i class="fas fa-comment-dots"></i> Comentarios adicionales
                                        </h4>
                                        <p class="text-sm text-gray-800 bg-white p-3 rounded-lg border border-gray-100">{{ $resultado->comentarios }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Aún no hay devoluciones registradas para este entrenamiento.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
