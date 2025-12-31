@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Mi Perfil']]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-500"></i>
            Mi Perfil
        </h1>
        <p class="text-gray-600 mt-1">Visualiza tu información y gestiona tu seguridad</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Perfil -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 text-center">
                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl mx-auto mb-4 border-4 border-white shadow-md">
                    @if($user->rol === 'alumno' && $alumno)
                        {{ substr($alumno->nombre, 0, 1) }}
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900">
                    @if($user->rol === 'alumno' && $alumno)
                        {{ $alumno->nombre }} {{ $alumno->apellido }}
                    @else
                        {{ $user->name }}
                    @endif
                </h2>
                <p class="text-sm text-blue-600 font-medium uppercase tracking-wider">{{ $user->rol }}</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Miembro desde</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Información Detallada -->
        <div class="md:col-span-2 space-y-6">
            <!-- Información Básica -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Información Personal
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nombre Completo</p>
                            <p class="text-sm font-medium text-gray-900">
                                @if($user->rol === 'alumno' && $alumno)
                                    {{ $alumno->nombre }} {{ $alumno->apellido }}
                                @else
                                    {{ $user->name }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Correo Electrónico</p>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900">{{ $user->email }}</p>
                            </div>
                        </div>

                        @if($user->rol === 'alumno' && $alumno)
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">DNI</p>
                                <p class="text-sm font-medium text-gray-900">{{ $alumno->dni ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Fecha de Nacimiento</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $alumno->fechaNacimiento ? $alumno->fechaNacimiento->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Sexo</p>
                                <p class="text-sm font-medium text-gray-900 capitalize">{{ $alumno->sexo }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Obra Social</p>
                                <p class="text-sm font-medium text-gray-900">{{ $alumno->obra_social ?? '-' }}</p>
                            </div>
                        @endif

                        @if($user->ciudad)
                            <div class="sm:col-span-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ubicación</p>
                                <p class="text-sm font-medium text-gray-900">{{ $user->ciudad }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actualizar Contraseña -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-lock text-blue-500"></i>
                        Seguridad
                    </h3>

                    <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                <input type="password" name="password" id="password" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                                <i class="fas fa-shield-alt"></i> Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
