@if($mostrarColumnaEquipo ?? true)
    <td class="px-6 py-4 font-medium text-gray-900">{{ $pago->equipo ? $pago->equipo->nombre : 'N/A' }}</td>
@endif
<td class="px-6 py-4 text-gray-600">{{ ucfirst(\Carbon\Carbon::parse($pago->mesCorrespondiente)->locale('es')->translatedFormat('F Y')) }}</td>
<td class="px-6 py-4 font-bold">${{ number_format($pago->monto, 2) }}</td>
<td class="px-6 py-4">
    @php
        $esVencido = $pago->estado === 'vencido' || ($pago->estado === 'pendiente' && ($pago->fechaVencimiento ? $pago->fechaVencimiento->isPast() : $pago->mesCorrespondiente < now()->format('Y-m')));
    @endphp
    <span class="px-2 py-1 text-xs rounded-full {{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-700' : ($pago->estado === 'cancelado' ? 'bg-gray-100 text-gray-700' : ($esVencido ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
        {{ $esVencido && $pago->estado === 'pendiente' ? 'Vencido' : ucfirst($pago->estado) }}
    </span>
</td>
<td class="px-6 py-4 text-gray-600">
    {{ $pago->fechaVencimiento ? \Carbon\Carbon::parse($pago->fechaVencimiento)->format('d/m/Y') : 'N/A' }}
</td>
<td class="px-6 py-4 text-gray-600">
    {{ $pago->fechaPago ? \Carbon\Carbon::parse($pago->fechaPago)->format('d/m/Y') : '-' }}
</td>
<td class="px-6 py-4 text-gray-600">
    {{ $pago->notas ?? '-' }}
</td>
