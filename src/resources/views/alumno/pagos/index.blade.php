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
                    @forelse($pagos as $pago)
                        <tr>
                            <td class="px-6 py-4">
                                @php
                                    $teamName = \App\Models\Setting::get('team_name', null, $pago->profesorId);
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                        <i class="fas fa-users text-xs"></i>
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $teamName ?: $pago->profesor->nombre . ' ' . $pago->profesor->apellido }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ ucfirst(\Carbon\Carbon::parse($pago->mesCorrespondiente)->locale('es')->translatedFormat('F Y')) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">${{ number_format($pago->monto, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $esVencido = $pago->estado === 'vencido' || ($pago->estado === 'pendiente' && ($pago->fechaVencimiento ? $pago->fechaVencimiento->isPast() : $pago->mesCorrespondiente < now()->format('Y-m')));
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-700' : ($pago->estado === 'cancelado' ? 'bg-gray-100 text-gray-700' : ($esVencido ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ $esVencido && $pago->estado === 'pendiente' ? 'Vencido' : ucfirst($pago->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $pago->fechaVencimiento ? $pago->fechaVencimiento->format('d/m/Y') : '---' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $pago->fechaPago ? $pago->fechaPago->format('d/m/Y') : '---' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm italic">
                                {{ $pago->notas ?? 'Sin notas' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay registros de pagos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
