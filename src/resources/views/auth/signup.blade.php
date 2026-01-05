@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Crear Cuenta</h2>
            <p class="mt-2 text-sm text-gray-600">
                O <a href="{{ route('login', ['email' => $email ?? '']) }}" class="font-medium text-blue-600 hover:text-blue-500">inicia sesión si ya tienes cuenta</a>
            </p>
        </div>

        <form class="mt-8 space-y-4" action="/signup" method="POST" x-data="{ rol: '{{ old('rol', (isset($invitation_token) && $invitation_token) ? 'alumno' : 'alumno') }}' }">
            @csrf
            <input type="hidden" name="invitation_token" value="{{ $invitation_token ?? '' }}">
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input id="nombre" name="nombre" type="text" required value="{{ old('nombre') }}"
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Tu nombre">
                    </div>
                    <div>
                        <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                        <input id="apellido" name="apellido" type="text" :required="rol === 'alumno'" value="{{ old('apellido') }}"
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Tu apellido">
                    </div>
                </div>

                <div x-show="rol === 'alumno'" class="space-y-4" x-cloak>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                            <input id="dni" name="dni" type="text" :required="rol === 'alumno'" value="{{ old('dni') }}"
                                class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Número de DNI">
                        </div>
                    </div>
                    <div>
                        <label for="fechaNacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                        <input id="fechaNacimiento" name="fechaNacimiento" type="date" :required="rol === 'alumno'" value="{{ old('fechaNacimiento') }}"
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input id="email" name="email" type="email" required value="{{ $email ?? old('email') }}"
                        {{ isset($email) && $email ? 'readonly' : '' }}
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm {{ isset($email) && $email ? 'bg-gray-50' : '' }}"
                        placeholder="ejemplo@correo.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña (mín. 6 caracteres)</label>
                    <input id="password" name="password" type="password" required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="••••••">
                </div>
                <div>
                    @if(isset($invitation_token) && $invitation_token)
                        <input type="hidden" name="rol" value="alumno">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <div class="appearance-none relative block w-full px-3 py-3 border border-gray-300 bg-gray-50 text-gray-600 rounded-lg sm:text-sm">
                            Alumno (Invitación)
                        </div>
                    @else
                        <label for="rol" class="block text-sm font-medium text-gray-700 mb-1">Tipo de usuario</label>
                        <select id="rol" name="rol" x-model="rol" required
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                            <option value="alumno">Alumno</option>
                            <option value="profesor">Profesor</option>
                        </select>
                    @endif
                </div>
            </div>

            @if ($errors->any())
                <div class="text-red-500 text-sm mt-2">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Registrarse
                </button>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">O regístrate con</span>
                </div>
            </div>

            <div>
                <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <img class="h-5 w-5 mr-2" src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google logo">
                    Google
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
