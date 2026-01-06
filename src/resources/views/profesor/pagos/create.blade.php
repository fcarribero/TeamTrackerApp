@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Pagos', 'url' => route('pagos.index')],
        ['label' => 'Registrar Pago']
    ]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Registrar Pago</h1>
            <p class="text-gray-600">Registra un nuevo pago de alumno</p>
        </div>
        <a href="{{ route('pagos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form action="{{ route('pagos.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="alumnoId" class="text-sm font-semibold text-gray-700">Alumno</label>
                    <select name="alumnoId" id="alumnoId" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Selecciona un alumno</option>
                        @foreach($alumnos as $alumno)
                            <option value="{{ $alumno->id }}" {{ old('alumnoId', request('alumnoId')) == $alumno->id ? 'selected' : '' }}>
                                {{ $alumno->nombre }} {{$alumno->apellido}}
                            </option>
                        @endforeach
                    </select>
                    @error('alumnoId')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

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
                    <label for="fechaPago" class="text-sm font-semibold text-gray-700">Fecha de Pago</label>
                    <input type="date" name="fechaPago" id="fechaPago" value="{{ old('fechaPago', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fechaPago')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fechaVencimiento" class="text-sm font-semibold text-gray-700">Fecha de Vencimiento</label>
                    <input type="date" name="fechaVencimiento" id="fechaVencimiento" value="{{ old('fechaVencimiento') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('fechaVencimiento')
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
                    <label for="estado" class="text-sm font-semibold text-gray-700">Estado</label>
                    <select name="estado" id="estado" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ old('estado', 'pagado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="vencido" {{ old('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('estado')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="notas" class="text-sm font-semibold text-gray-700">Notas/Concepto</label>
                <textarea name="notas" id="notas" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                          placeholder="Detalles adicionales del pago...">{{ old('notas') }}</textarea>
                @error('notas')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('pagos.index') }}"
                   class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    <i class="fas fa-save mr-2"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
