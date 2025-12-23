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
                @php $resultado = $entrenamiento->resultados->first(); @endphp
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100" x-data="{ showFeedback: false }">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-500 p-3 rounded-lg text-white shadow-md">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $entrenamiento->titulo }}</h3>
                                    <p class="text-gray-700 mb-3 capitalize">
                                        {{ \Carbon\Carbon::parse($entrenamiento->fecha)->isoFormat('dddd, D [de] MMMM') }}
                                    </p>
                                </div>
                                @if($resultado)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Completado
                                    </span>
                                @endif
                            </div>

                            @if($entrenamiento->observaciones)
                                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4 shadow-sm rounded-r-lg">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fas fa-exclamation-circle text-amber-600"></i>
                                        <span class="font-bold text-amber-900 text-sm">Observaciones del Profesor:</span>
                                    </div>
                                    <p class="text-amber-800 text-sm italic">{{ $entrenamiento->observaciones }}</p>
                                </div>
                            @endif

                            @if($entrenamiento->contenidoPersonalizado)
                                <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm mb-4">
                                    <div class="flex items-center gap-2 mb-3 border-b border-blue-50 pb-2">
                                        <i class="fas fa-dumbbell text-blue-600"></i>
                                        <span class="font-bold text-gray-900">Detalles del Entrenamiento</span>
                                    </div>
                                    @if(is_array($entrenamiento->contenidoPersonalizado))
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @if(!empty($entrenamiento->contenidoPersonalizado['calentamiento']))
                                            <div>
                                                <h4 class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Calentamiento</h4>
                                                <div class="space-y-1">
                                                    @foreach($entrenamiento->contenidoPersonalizado['calentamiento'] as $ej)
                                                        <div class="text-xs bg-orange-50 text-orange-800 p-2 rounded-lg border border-orange-100">{{ $ej }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            @if(!empty($entrenamiento->contenidoPersonalizado['trabajo_principal']))
                                            <div>
                                                <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2">Trabajo Principal</h4>
                                                <div class="space-y-1">
                                                    @foreach($entrenamiento->contenidoPersonalizado['trabajo_principal'] as $ej)
                                                        <div class="text-xs bg-blue-50 text-blue-800 p-2 rounded-lg border border-blue-100">{{ $ej }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            @if(!empty($entrenamiento->contenidoPersonalizado['enfriamiento']))
                                            <div>
                                                <h4 class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-2">Enfriamiento</h4>
                                                <div class="space-y-1">
                                                    @foreach($entrenamiento->contenidoPersonalizado['enfriamiento'] as $ej)
                                                        <div class="text-xs bg-green-50 text-green-800 p-2 rounded-lg border border-green-100">{{ $ej }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $entrenamiento->contenidoPersonalizado }}</p>
                                    @endif
                                </div>
                            @endif

                            @if(!$resultado)
                                <button @click="showFeedback = !showFeedback" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 font-bold shadow-lg shadow-blue-200">
                                    <i class="fas fa-check"></i>
                                    Marcar como completado
                                </button>
                            @else
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 shadow-inner">
                                    <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                        <i class="fas fa-comment-dots text-blue-500"></i> Tu Devolución
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Dificultad Percibida</p>
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full {{ $resultado->dificultad <= 3 ? 'bg-green-500' : ($resultado->dificultad <= 6 ? 'bg-yellow-500' : ($resultado->dificultad <= 8 ? 'bg-orange-500' : 'bg-red-500')) }}"
                                                         style="width: {{ $resultado->dificultad * 10 }}%"></div>
                                                </div>
                                                <span class="text-sm font-bold text-gray-700">{{ $resultado->dificultad }}/10</span>
                                            </div>
                                        </div>
                                        @if($resultado->sensacion)
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Cómo me sentí</p>
                                            <p class="text-sm text-gray-700">{{ $resultado->sensacion }}</p>
                                        </div>
                                        @endif
                                        @if($resultado->molestias)
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Molestias</p>
                                            <p class="text-sm text-gray-700">{{ $resultado->molestias }}</p>
                                        </div>
                                        @endif
                                        @if($resultado->comentarios)
                                        <div class="md:col-span-2">
                                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Comentarios Adicionales</p>
                                            <p class="text-sm text-gray-700">{{ $resultado->comentarios }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    <button @click="showFeedback = !showFeedback" class="mt-4 text-xs text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fas fa-edit"></i> Editar devolución
                                    </button>
                                </div>
                            @endif

                            <!-- Formulario de Feedback -->
                            <div x-show="showFeedback" x-transition class="mt-6 bg-white rounded-xl p-6 border-2 border-blue-200 shadow-xl">
                                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-star text-yellow-400"></i>
                                    {{ $resultado ? 'Editar mi devolución' : '¿Cómo te fue en el entrenamiento?' }}
                                </h4>
                                <form action="{{ route('alumno.entrenamientos.completar', $entrenamiento->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cómo me sentí</label>
                                        <textarea name="sensacion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm" placeholder="Ej. Con falta de energía durante la parte principal...">{{ $resultado->sensacion ?? '' }}</textarea>
                                    </div>

                                    <div x-data="{ dificultad: {{ $resultado->dificultad ?? 5 }} }">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1 flex justify-between">
                                            Dificultad percibida
                                            <span class="text-blue-600 font-bold" x-text="dificultad + '/10'"></span>
                                        </label>
                                        <input type="range" name="dificultad" min="1" max="10" x-model="dificultad"
                                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600 transition-all"
                                               :class="dificultad <= 3 ? 'accent-green-500' : (dificultad <= 6 ? 'accent-yellow-500' : (dificultad <= 8 ? 'accent-orange-500' : 'accent-red-500'))">
                                        <div class="flex justify-between text-[10px] text-gray-400 px-1 mt-1 font-bold uppercase">
                                            <span>Muy Fácil</span>
                                            <span>Extremo</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dolores o molestias</label>
                                        <input type="text" name="molestias" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm" placeholder="Ej. La rodilla me molestó un poco" value="{{ $resultado->molestias ?? '' }}">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Comentarios adicionales</label>
                                        <textarea name="comentarios" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm" placeholder="Hacía mucho calor...">{{ $resultado->comentarios ?? '' }}</textarea>
                                    </div>

                                    <div class="flex gap-3 pt-2">
                                        <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-bold transition shadow-lg shadow-green-100">
                                            Guardar Devolución
                                        </button>
                                        <button type="button" @click="showFeedback = false" class="px-6 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
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
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-gray-400"></i>
                Entrenamientos Anteriores ({{ count($pasados) }})
            </h2>
            <div class="space-y-4">
                @foreach($pasados->take(10) as $entrenamiento)
                    @php $resultado = $entrenamiento->resultados->first(); @endphp
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200" x-data="{ showFeedback: false }">
                        <div class="flex items-start gap-4">
                            <div class="bg-gray-400 p-3 rounded-lg text-white shadow-md">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $entrenamiento->titulo }}</h3>
                                        <p class="text-gray-600 mb-3 capitalize">
                                            {{ \Carbon\Carbon::parse($entrenamiento->fecha)->isoFormat('dddd, D [de] MMMM') }}
                                        </p>
                                    </div>
                                    @if($resultado)
                                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i> Completado
                                        </span>
                                    @endif
                                </div>

                                @if($entrenamiento->observaciones)
                                    <div class="bg-gray-100 border-l-4 border-gray-300 p-4 mb-4 shadow-sm rounded-r-lg">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i class="fas fa-exclamation-circle text-gray-400"></i>
                                            <span class="font-bold text-gray-600 text-sm">Observaciones del Profesor:</span>
                                        </div>
                                        <p class="text-gray-600 text-sm italic">{{ $entrenamiento->observaciones }}</p>
                                    </div>
                                @endif

                                @if($entrenamiento->contenidoPersonalizado)
                                    <div class="bg-white rounded-lg p-4 border border-gray-100 shadow-sm mb-4">
                                        <div class="flex items-center gap-2 mb-3 border-b border-gray-50 pb-2">
                                            <i class="fas fa-dumbbell text-gray-400"></i>
                                            <span class="font-bold text-gray-900">Detalles del Entrenamiento</span>
                                        </div>
                                        @if(is_array($entrenamiento->contenidoPersonalizado))
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                @if(!empty($entrenamiento->contenidoPersonalizado['calentamiento']))
                                                <div>
                                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Calentamiento</h4>
                                                    <div class="space-y-1">
                                                        @foreach($entrenamiento->contenidoPersonalizado['calentamiento'] as $ej)
                                                            <div class="text-xs bg-gray-50 text-gray-700 p-2 rounded-lg border border-gray-100">{{ $ej }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif

                                                @if(!empty($entrenamiento->contenidoPersonalizado['trabajo_principal']))
                                                <div>
                                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Trabajo Principal</h4>
                                                    <div class="space-y-1">
                                                        @foreach($entrenamiento->contenidoPersonalizado['trabajo_principal'] as $ej)
                                                            <div class="text-xs bg-gray-50 text-gray-700 p-2 rounded-lg border border-gray-100">{{ $ej }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif

                                                @if(!empty($entrenamiento->contenidoPersonalizado['enfriamiento']))
                                                <div>
                                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Enfriamiento</h4>
                                                    <div class="space-y-1">
                                                        @foreach($entrenamiento->contenidoPersonalizado['enfriamiento'] as $ej)
                                                            <div class="text-xs bg-gray-50 text-gray-700 p-2 rounded-lg border border-gray-100">{{ $ej }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $entrenamiento->contenidoPersonalizado }}</p>
                                        @endif
                                    </div>
                                @endif

                                @if(!$resultado)
                                    <button @click="showFeedback = !showFeedback" class="w-full md:w-auto bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition flex items-center justify-center gap-2 font-bold shadow-lg shadow-gray-200">
                                        <i class="fas fa-check"></i>
                                        Registrar devolución
                                    </button>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 shadow-inner">
                                        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                            <i class="fas fa-comment-dots text-gray-400"></i> Tu Devolución
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Dificultad Percibida</p>
                                                <div class="flex items-center gap-2">
                                                    <div class="h-2 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                                        <div class="h-full bg-gray-400"
                                                             style="width: {{ $resultado->dificultad * 10 }}%"></div>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-700">{{ $resultado->dificultad }}/10</span>
                                                </div>
                                            </div>
                                            @if($resultado->sensacion)
                                            <div>
                                                <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Cómo me sentí</p>
                                                <p class="text-sm text-gray-700">{{ $resultado->sensacion }}</p>
                                            </div>
                                            @endif
                                            @if($resultado->molestias)
                                            <div>
                                                <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Molestias</p>
                                                <p class="text-sm text-gray-700">{{ $resultado->molestias }}</p>
                                            </div>
                                            @endif
                                            @if($resultado->comentarios)
                                            <div class="md:col-span-2">
                                                <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Comentarios Adicionales</p>
                                                <p class="text-sm text-gray-700">{{ $resultado->comentarios }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        <button @click="showFeedback = !showFeedback" class="mt-4 text-xs text-gray-600 hover:underline flex items-center gap-1">
                                            <i class="fas fa-edit"></i> Editar devolución
                                        </button>
                                    </div>
                                @endif

                                <!-- Formulario de Feedback -->
                                <div x-show="showFeedback" x-transition class="mt-6 bg-white rounded-xl p-6 border-2 border-gray-300 shadow-xl">
                                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="fas fa-star text-gray-400"></i>
                                        {{ $resultado ? 'Editar mi devolución' : '¿Cómo te fue en el entrenamiento?' }}
                                    </h4>
                                    <form action="{{ route('alumno.entrenamientos.completar', $entrenamiento->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cómo me sentí</label>
                                            <textarea name="sensacion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 outline-none transition text-sm" placeholder="Ej. Con falta de energía durante la parte principal...">{{ $resultado->sensacion ?? '' }}</textarea>
                                        </div>

                                        <div x-data="{ dificultad: {{ $resultado->dificultad ?? 5 }} }">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1 flex justify-between">
                                                Dificultad percibida
                                                <span class="text-gray-600 font-bold" x-text="dificultad + '/10'"></span>
                                            </label>
                                            <input type="range" name="dificultad" min="1" max="10" x-model="dificultad"
                                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-gray-600 transition-all">
                                            <div class="flex justify-between text-[10px] text-gray-400 px-1 mt-1 font-bold uppercase">
                                                <span>Muy Fácil</span>
                                                <span>Extremo</span>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Dolores o molestias</label>
                                            <input type="text" name="molestias" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 outline-none transition text-sm" placeholder="Ej. La rodilla me molestó un poco" value="{{ $resultado->molestias ?? '' }}">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Comentarios adicionales</label>
                                            <textarea name="comentarios" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 outline-none transition text-sm" placeholder="Hacía mucho calor...">{{ $resultado->comentarios ?? '' }}</textarea>
                                        </div>

                                        <div class="flex gap-3 pt-2">
                                            <button type="submit" class="flex-1 bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 font-bold transition shadow-lg shadow-gray-100">
                                                Guardar Devolución
                                            </button>
                                            <button type="button" @click="showFeedback = false" class="px-6 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
