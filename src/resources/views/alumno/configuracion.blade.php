@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-cog text-blue-500"></i>
            Configuración
        </h1>
        <p class="text-gray-600 mt-1">Gestiona tus conexiones y preferencias</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Conexión con Garmin -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="bg-blue-600 p-3 rounded-xl shadow-lg text-white">
                        <i class="fas fa-running fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Garmin Connect</h2>
                        <p class="text-sm text-gray-500">Sincroniza tus actividades automáticamente</p>
                    </div>
                </div>

                @if($alumno->garminAccount)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-3 text-green-700">
                            <i class="fas fa-check-circle text-xl"></i>
                            <div>
                                <p class="font-bold">Cuenta conectada</p>
                                <p class="text-xs">Tus actividades se importarán automáticamente.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                            <span class="text-gray-500">Usuario de Garmin:</span>
                            <span class="font-medium text-gray-900">{{ $alumno->garminAccount->garmin_user_id }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                            <span class="text-gray-500">Última sincronización:</span>
                            <span class="font-medium text-gray-900">
                                {{ $alumno->garminActivities()->latest()->first()?->created_at?->diffForHumans() ?? 'Nunca' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <form action="{{ route('auth.garmin.disconnect') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-50 text-red-600 hover:bg-red-100 font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-unlink"></i> Desconectar Garmin
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 text-sm text-gray-600">
                        Al conectar tu cuenta de Garmin Connect, tus entrenamientos y actividades se importarán automáticamente a TeamTracker.
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('auth.garmin') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                            <i class="fab fa-connectdevelop"></i> Conectar con Garmin Connect
                        </a>
                    </div>
                @endif
            </div>

            @if($alumno->garminAccount && $alumno->garminActivities()->count() > 0)
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                        <i class="fas fa-history text-blue-500"></i> Últimas actividades importadas
                    </h3>
                    <div class="space-y-3">
                        @foreach($alumno->garminActivities()->latest()->take(5)->get() as $activity)
                            <div class="bg-white p-3 rounded-lg border border-gray-200 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                        @if($activity->activity_type == 'RUNNING')
                                            <i class="fas fa-running"></i>
                                        @elseif($activity->activity_type == 'CYCLING')
                                            <i class="fas fa-bicycle"></i>
                                        @else
                                            <i class="fas fa-walking"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $activity->name ?? 'Actividad sin nombre' }}</p>
                                        <p class="text-gray-500">{{ $activity->start_time->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right text-blue-600 font-bold">
                                    {{ number_format($activity->distance / 1000, 2) }} km
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Perfil (Placeholder) -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 opacity-50">
            <div class="p-6 text-center py-12">
                <i class="fas fa-user-circle fa-4x text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-400">Perfil de Usuario</h3>
                <p class="text-sm text-gray-400">Próximamente podrás editar tus datos personales aquí.</p>
            </div>
        </div>
    </div>
</div>
@endsection
