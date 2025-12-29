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
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="fecha" id="fecha" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

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
                                    <h3 class="text-xl font-bold text-gray-900">{{ $competencia->nombre }}</h3>
                                    <p class="text-gray-500 flex items-center gap-2">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ $competencia->fecha->format('d/m/Y') }}
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $competencia->fecha->isPast() ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700' }}">
                                            {{ $competencia->fecha->isPast() ? 'Finalizada' : 'Próxima' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

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
