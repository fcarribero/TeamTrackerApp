@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Seleccionar Grupo</h2>
            <p class="mt-2 text-sm text-gray-600">
                Perteneces a varios grupos, por favor selecciona uno para continuar.
            </p>
        </div>

        <div class="mt-8 space-y-4">
            @foreach($grupos as $grupo)
                <form action="{{ route('grupos.set') }}" method="POST">
                    @csrf
                    <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                    <button type="submit" class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $grupo->color ?? '#3B82F6' }}"></div>
                            <span class="text-lg font-semibold text-gray-700 group-hover:text-blue-700">{{ $grupo->nombre }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
