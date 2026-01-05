@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Mis Pagos']]])
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-credit-card text-blue-500"></i>
            Mis Pagos
        </h1>
        <p class="text-gray-600 mt-1">Historial de mensualidades y estado de cuenta</p>
    </div>

    <div x-data="{ activeTab: 'pendientes' }" class="space-y-4">
        <!-- Pestañas -->
        <div class="flex border-b border-gray-200">
            <button @click="activeTab = 'pendientes'"
                    :class="activeTab === 'pendientes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Pendientes
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold {{ $pagos->whereIn('estado', ['pendiente', 'vencido'])->count() > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}">
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

        <div id="tour-tabla-pagos" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Equipo</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Mes Correspondiente</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Monto</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Estado</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Vencimiento</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Fecha de Pago</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Notas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $pagosPendientes = $pagos->filter(fn($p) => in_array($p->estado, ['pendiente', 'vencido']));
                            $pagosRealizados = $pagos->where('estado', 'pagado');
                        @endphp

                        {{-- Pestaña Pendientes --}}
                        @foreach($pagosPendientes as $pago)
                            <tr x-show="activeTab === 'pendientes'" x-cloak>
                                @include('alumno.pagos._row', ['pago' => $pago])
                            </tr>
                        @endforeach
                        @if($pagosPendientes->isEmpty())
                            <tr x-show="activeTab === 'pendientes'" x-cloak>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No tienes pagos pendientes</td>
                            </tr>
                        @endif

                        {{-- Pestaña Realizados --}}
                        @foreach($pagosRealizados as $pago)
                            <tr x-show="activeTab === 'realizados'" x-cloak>
                                @include('alumno.pagos._row', ['pago' => $pago])
                            </tr>
                        @endforeach
                        @if($pagosRealizados->isEmpty())
                            <tr x-show="activeTab === 'realizados'" x-cloak>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No tienes pagos realizados</td>
                            </tr>
                        @endif

                        {{-- Pestaña Todos --}}
                        @foreach($pagos as $pago)
                            <tr x-show="activeTab === 'todos'" x-cloak>
                                @include('alumno.pagos._row', ['pago' => $pago])
                            </tr>
                        @endforeach
                        @if($pagos->isEmpty())
                            <tr x-show="activeTab === 'todos'" x-cloak>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No hay registros de pagos</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
