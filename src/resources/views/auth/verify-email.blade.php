@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Verifica tu email</h2>
            <p class="mt-2 text-sm text-gray-600">
                Gracias por registrarte! Antes de comenzar, por favor verifica tu dirección de correo haciendo clic en el link que te enviamos. Si no recibiste el correo, podemos enviarte otro.
            </p>
        </div>

        @if (session('message'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <div class="mt-8 space-y-4">
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Reenviar correo de verificación
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
