<td class="px-6 py-4 font-medium text-gray-900">{{ $pago->alumno ? $pago->alumno->nombre . ' ' . $pago->alumno->apellido : 'N/A' }}</td>
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
