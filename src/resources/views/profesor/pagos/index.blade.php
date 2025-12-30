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
        <a href="{{ route('pagos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Registrar Pago
        </a>
    </div>

    <!-- Dashboard de Pagos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Alumno</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Mes</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Monto</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Estado</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900">Fecha</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pagos as $pago)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $pago->alumno->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ ucfirst(\Carbon\Carbon::parse($pago->mesCorrespondiente)->locale('es')->translatedFormat('F Y')) }}</td>
                            <td class="px-6 py-4 font-bold">${{ $pago->monto }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $esVencido = $pago->estado === 'vencido' || ($pago->estado === 'pendiente' && $pago->mesCorrespondiente < now()->format('Y-m'));
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-700' : ($esVencido ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $esVencido && $pago->estado === 'pendiente' ? 'Vencido' : ucfirst($pago->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($pago->fechaPago)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('pagos.edit', $pago->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este pago?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay pagos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
