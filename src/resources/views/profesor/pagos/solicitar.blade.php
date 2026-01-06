@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Pagos', 'url' => route('pagos.index')],
        ['label' => 'Solicitar Pagos Masivos']
    ]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Solicitar Pagos Masivos</h1>
            <p class="text-gray-600">Crea solicitudes de pago para todos los alumnos de un grupo</p>
        </div>
        <a href="{{ route('pagos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('pagos.solicitar.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="space-y-4">
                <label class="text-sm font-semibold text-gray-700 block">Seleccionar Grupos</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    @forelse($grupos as $grupo)
                        <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-100 hover:border-blue-300 transition cursor-pointer">
                            <input type="checkbox" name="grupos[]" value="{{ $grupo->id }}" {{ is_array(old('grupos')) && in_array($grupo->id, old('grupos')) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <x-group-tag :grupo="$grupo" />
                        </label>
                    @empty
                        <p class="col-span-full text-center text-gray-500 py-4 italic text-sm">No tienes grupos creados</p>
                    @endforelse
                </div>
                @error('grupos')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label for="monto" class="text-sm font-semibold text-gray-700">Monto</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" step="0.01" name="monto" id="monto" value="{{ old('monto') }}" required
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                               placeholder="0.00">
                    </div>
                    @error('monto')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="mesCorrespondiente" class="text-sm font-semibold text-gray-700">Mes Correspondiente</label>
                    <input type="month" name="mesCorrespondiente" id="mesCorrespondiente" value="{{ old('mesCorrespondiente', date('Y-m')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('mesCorrespondiente')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fechaVencimiento" class="text-sm font-semibold text-gray-700">Fecha de Vencimiento</label>
                    <input type="date" name="fechaVencimiento" id="fechaVencimiento" value="{{ old('fechaVencimiento', date('Y-m-d', strtotime('+7 days'))) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fechaVencimiento')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 bg-blue-50 p-4 rounded-lg">
                <input type="checkbox" name="cancelarPrevios" id="cancelarPrevios" value="1" {{ old('cancelarPrevios') ? 'checked' : '' }}
                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="cancelarPrevios" class="text-sm text-blue-800">
                    <span class="font-bold">Cancelar solicitudes previas:</span> Si ya existe una solicitud de pago para este periodo que esté pendiente, se cancelará automáticamente antes de crear la nueva.
                </label>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('pagos.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                    Solicitar Pagos
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
