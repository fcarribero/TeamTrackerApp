@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Pagos']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pagos</h1>
            <p class="text-gray-600">Control de mensualidades e ingresos</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pagos.solicitar') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-bullhorn"></i>
                Solicitar Pagos
            </a>
        </div>
    </div>

    <div id="tour-pagos-stats" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-lg text-green-600">
                    <i class="fas fa-dollar-sign fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Ingresos del Mes</h3>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['ingresos_mes'], 2) }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-100 p-3 rounded-lg text-yellow-600">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Pendientes del Mes</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['pendientes_mes'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-red-100 p-3 rounded-lg text-red-600">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Pagos Vencidos</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['vencidos'] }}</p>
        </div>
    </div>

    <!-- Buscador -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('pagos.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="Buscar por nombre o apellido del alumno...">
            </div>
            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('pagos.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium flex items-center justify-center">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    @if($alumnosVencidos->isNotEmpty())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-user-clock text-red-600 mt-1"></i>
                <div>
                    <h3 class="text-red-800 font-bold">Alumnos con deudas</h3>
                    <p class="text-red-700 text-sm">
                        Los siguientes alumnos tienen pagos vencidos o pendientes de meses anteriores:
                        <span class="font-bold">{{ $alumnosVencidos->pluck('nombre')->implode(', ') }}</span>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div x-data="{ activeTab: '{{ request('search') ? 'todos' : 'pendientes' }}' }" class="space-y-4">
        <!-- Pestañas -->
        @if(!request('search'))
        <div class="flex border-b border-gray-200">
            <button @click="activeTab = 'pendientes'"
                    :class="activeTab === 'pendientes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Pendientes
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold {{ $stats['pendientes_mes'] + $stats['vencidos'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $pagos->whereIn('estado', ['pendiente', 'vencido'])->count() }}
                </span>
            </button>
            <button @click="activeTab = 'realizados'"
                    :class="activeTab === 'realizados' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Realizados
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    {{ $pagos->where('estado', 'pagado')->count() }}
                </span>
            </button>
            <button @click="activeTab = 'todos'"
                    :class="activeTab === 'todos' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Todos
            </button>
        </div>
        @endif

        <div id="tour-lista-pagos" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Alumno</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Mes</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Monto</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Estado</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Vencimiento</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(request('search'))
                            @foreach($pagos as $pago)
                                <tr>
                                    @include('profesor.pagos._row', ['pago' => $pago])
                                </tr>
                            @endforeach
                            @if($pagos->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No se encontraron pagos para "{{ request('search') }}"</td>
                                </tr>
                            @endif
                        @else
                            @php
                                $pagosPendientes = $pagos->filter(fn($p) => in_array($p->estado, ['pendiente', 'vencido']));
                                $pagosRealizados = $pagos->where('estado', 'pagado');
                            @endphp

                            {{-- Pestaña Pendientes --}}
                            @foreach($pagosPendientes as $pago)
                                <tr x-show="activeTab === 'pendientes'" x-cloak>
                                    @include('profesor.pagos._row', ['pago' => $pago])
                                </tr>
                            @endforeach
                            @if($pagosPendientes->isEmpty())
                                <tr x-show="activeTab === 'pendientes'" x-cloak>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay pagos pendientes</td>
                                </tr>
                            @endif

                            {{-- Pestaña Realizados --}}
                            @foreach($pagosRealizados as $pago)
                                <tr x-show="activeTab === 'realizados'" x-cloak>
                                    @include('profesor.pagos._row', ['pago' => $pago])
                                </tr>
                            @endforeach
                            @if($pagosRealizados->isEmpty())
                                <tr x-show="activeTab === 'realizados'" x-cloak>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay pagos realizados</td>
                                </tr>
                            @endif

                            {{-- Pestaña Todos --}}
                            @foreach($pagos as $pago)
                                <tr x-show="activeTab === 'todos'" x-cloak>
                                    @include('profesor.pagos._row', ['pago' => $pago])
                                </tr>
                            @endforeach
                            @if($pagos->isEmpty())
                                <tr x-show="activeTab === 'todos'" x-cloak>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay pagos registrados</td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
