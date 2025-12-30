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
                O <a href="/login" class="font-medium text-blue-600 hover:text-blue-500">inicia sesión si ya tienes cuenta</a>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="/signup" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="nombre" class="sr-only">Nombre completo</label>
                    <input id="nombre" name="nombre" type="text" required value="{{ old('nombre') }}"
                        class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Nombre completo">
                </div>
                <div>
                    <label for="email" class="sr-only">Correo electrónico</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                        class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Correo electrónico">
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña (mín. 6 caracteres)</label>
                    <input id="password" name="password" type="password" required
                        class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Contraseña (mín. 6 caracteres)">
                </div>
                <div>
                    <label for="rol" class="sr-only">Tipo de usuario</label>
                    <select id="rol" name="rol" required
                        class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                        <option value="alumno">Alumno</option>
                        <option value="profesor">Profesor</option>
                    </select>
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
