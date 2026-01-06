@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Alumnos', 'url' => route('alumnos.index')],
        ['label' => $alumno->nombre . ' ' . $alumno->apellido]
    ]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('alumnos.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-bold text-gray-900">{{ $alumno->nombre }} {{ $alumno->apellido }}</h1>
                <x-new-user-badge :user="$alumno" />
            </div>
        </div>
        <a href="{{ route('alumnos.edit', $alumno->id) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fas fa-edit"></i>
            Editar Alumno
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex flex-col items-center text-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl mb-4 overflow-hidden border-4 border-white shadow-md shrink-0">
                        @if($alumno->image)
                            <img src="{{ asset('storage/' . $alumno->image) }}" alt="{{ $alumno->nombre }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($alumno->nombre, 0, 1) }}
                        @endif
                    </div>
                    <h2 class="text-xl font-bold">{{ $alumno->nombre }} {{ $alumno->apellido }}</h2>
                    <p class="text-gray-500 text-sm mb-1">{{ $alumno->dni ? 'DNI: ' . $alumno->dni : 'Sin DNI' }}</p>
                    <p class="text-gray-500 capitalize text-sm">{{ $alumno->sexo }}</p>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Obra Social / Plan</p>
                        <p class="text-gray-900 font-medium">{{ $alumno->obra_social ?? 'No especificado' }}</p>
                        @if($alumno->numero_socio)
                            <p class="text-xs text-gray-500">Socio: {{ $alumno->numero_socio }}</p>
                        @endif
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-800 uppercase font-bold mb-2 flex items-center gap-1">
                            <i class="fas fa-file-medical"></i> Certificado Médico
                        </p>
                        @if($alumno->certificado_medico)
                            <a href="{{ Storage::url($alumno->certificado_medico) }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-2 font-bold">
                                <i class="fas fa-download"></i> Descargar / Ver
                            </a>
                            <p class="text-[10px] mt-2 {{ $alumno->vencimiento_certificado && $alumno->vencimiento_certificado->isPast() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                Vence: {{ $alumno->vencimiento_certificado ? $alumno->vencimiento_certificado->format('d/m/Y') : 'Sin fecha' }}
                                @if($alumno->vencimiento_certificado && $alumno->vencimiento_certificado->isPast())
                                    <span class="block text-[8px] uppercase mt-0.5">⚠️ Vencido</span>
                                @endif
                            </p>
                        @else
                            <p class="text-xs text-gray-500 italic">No cargado</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Fecha de Nacimiento</p>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($alumno->fechaNacimiento)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Miembro desde</p>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($alumno->created_at)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Grupos</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($alumno->grupos as $grupo)
                                <x-group-tag :grupo="$grupo" />
                            @empty
                                <span class="text-xs text-gray-500 italic">Sin grupos</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-bold mb-4">Notas y Observaciones</h3>
                <div class="p-4 bg-gray-50 rounded-lg text-gray-700 min-h-[100px]">
                    {{ $alumno->notas ?? 'Sin observaciones' }}
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">Últimos Pagos</h3>
                    <a href="{{ route('pagos.create') }}?alumnoId={{ $alumno->id }}" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition">
                        Registrar Pago
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($alumno->pagos->take(5) as $pago)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border-b border-gray-50 last:border-0 cursor-pointer transition"
                             onclick="window.location='{{ route('pagos.edit', $pago->id) }}'">
                            <div>
                                <p class="font-semibold text-gray-900">${{ number_format($pago->monto, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(\Carbon\Carbon::parse($pago->mesCorrespondiente)->locale('es')->translatedFormat('F Y')) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-[10px] rounded-full {{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($pago->estado) }}
                                </span>
                                <p class="text-[10px] text-gray-400 mt-1">{{ $pago->fechaPago ? $pago->fechaPago->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            No hay pagos registrados
                        </div>
                    @endforelse
                </div>
                @if($alumno->pagos->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('pagos.index') }}?alumno_id={{ $alumno->id }}" class="text-sm text-blue-600 hover:underline">Ver todos los pagos</a>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">Calendario de Entrenamientos</h3>
                    <a href="{{ route('entrenamientos.create') }}?alumnoId={{ $alumno->id }}" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition">
                        Asignar Nuevo
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($entrenamientos->take(10) as $entrenamiento)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location='{{ route('entrenamientos.edit', $entrenamiento->id) }}'">
                            <div class="bg-blue-100 p-2 rounded-lg text-blue-600 text-center w-10">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm text-gray-900">{{ $entrenamiento->titulo }}</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-xs text-gray-500">{{ $entrenamiento->plantillaNombre ?? 'Personalizado' }}</p>
                                    @if($entrenamiento->grupos->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($entrenamiento->grupos as $grupo)
                                                <x-group-tag :grupo="$grupo" />
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-900">{{ $entrenamiento->fecha->format('d M Y') }}</p>
                                <p class="text-[10px] text-gray-500">{{ $entrenamiento->fecha->format('H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            No hay entrenamientos programados
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">Competencias</h3>
                    <a href="{{ route('competencias.index') }}" class="text-xs text-blue-600 hover:underline">Ver todas</a>
                </div>
                <div class="space-y-3">
                    @forelse($alumno->competencias->sortBy('fecha')->take(5) as $competencia)
                        <div class="flex items-center gap-3 p-3 bg-blue-50/50 rounded-lg border border-blue-100/50 hover:bg-blue-50 transition cursor-pointer" onclick="window.location='{{ route('competencias.edit', $competencia->id) }}'">
                            <div class="bg-blue-100 p-2 rounded-lg text-blue-600 w-10 text-center">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm text-gray-900">{{ $competencia->nombre }}</p>
                                <p class="text-[10px] text-gray-500">{{ $competencia->plan_carrera ? 'Plan cargado' : 'Pendiente de plan' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-900">{{ $competencia->fecha->format('d M Y') }}</p>
                                <span class="text-[8px] font-bold px-1.5 py-0.5 rounded-full {{ $competencia->fecha->isPast() ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700' }}">
                                    {{ $competencia->fecha->isPast() ? 'Fin' : 'Prox' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            No hay competencias registradas
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
