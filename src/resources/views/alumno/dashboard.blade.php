@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs')
@endsection

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">¡Bienvenido, {{ $alumno->nombre }}!</h1>
        <p class="text-gray-600">Aquí puedes ver tus entrenamientos y el estado de tus pagos</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Próximos Entrenamientos</h3>
            <p class="text-3xl font-bold text-gray-900">{{ count($proximosEntrenamientos) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Proximos Entrenamientos -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clock text-blue-500"></i>
                    Mis Próximos Entrenamientos
                </h2>
                <a href="/dashboard/alumno/entrenamientos" class="text-blue-500 hover:text-blue-600 text-sm font-medium">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($proximosEntrenamientos as $entrenamiento)
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                        <div class="bg-blue-500 p-2 rounded-lg text-white">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $entrenamiento->titulo }}</p>
                            <p class="text-sm text-gray-600">{{ $entrenamiento->plantilla->nombre ?? 'Personalizado' }}</p>
                            @if(isset($profesor) && $profesor->latitud)
                                @php
                                    $clima = app(\App\Services\WeatherService::class)->getDailyForecast((float)$profesor->latitud, (float)$profesor->longitud, \Carbon\Carbon::parse($entrenamiento->fecha));
                                @endphp
                                @if($clima)
                                    <div class="mt-1 flex items-center gap-2 text-[10px] text-blue-600 font-bold">
                                        <i class="fas {{ ($clima->is_historical ?? false) ? 'fa-history' : 'fa-cloud-sun' }}"></i>
                                        <span>{{ $clima->min }}°/{{ $clima->max }}°C</span>
                                        @if(!($clima->is_historical ?? false))
                                            <span class="text-gray-400 font-normal border-l pl-2">{{ $clima->tarde }}</span>
                                        @else
                                            <span class="text-gray-400 font-normal border-l pl-2 italic">Ref. Histórica</span>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($entrenamiento->fecha)->format('d M') }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($entrenamiento->fecha)->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No tienes entrenamientos programados</p>
                @endforelse
            </div>
        </div>

        <!-- Estado de Pago -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-credit-card text-blue-500"></i>
                    Estado de Pago - Mes Actual
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ now()->isoFormat('MMMM YYYY') }}</p>
            </div>
            <div class="space-y-3">
                @forelse($pagosMesActual as $pago)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="{{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} p-2 rounded-lg w-10 h-10 flex items-center justify-center">
                            <i class="fas {{ $pago->estado === 'pagado' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">${{ $pago->monto }}</p>
                            @php
                                $teamName = \App\Models\Setting::get('team_name', null, $pago->profesorId);
                            @endphp
                            <p class="text-xs text-blue-600 font-semibold">{{ $teamName ?: $pago->profesor->name }}</p>
                            <p class="text-sm text-gray-600">{{ ucfirst(\Carbon\Carbon::parse($pago->mesCorrespondiente)->locale('es')->translatedFormat('F Y')) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-2 py-1 rounded-full {{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($pago->estado) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $pago->fechaPago ? $pago->fechaPago->format('d/m/Y') : '---' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No hay pagos registrados para este mes</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notas del Profesor -->
    @if($alumno->notas)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Notas del Profesor</h2>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $alumno->notas }}</p>
        </div>
    @endif
</div>
@endsection
