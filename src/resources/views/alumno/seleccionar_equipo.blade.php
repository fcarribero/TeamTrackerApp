@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Seleccionar Equipo</h2>
            <p class="mt-2 text-sm text-gray-600">
                Perteneces a varios equipos de entrenamiento, por favor selecciona uno para continuar.
            </p>
        </div>

        <div class="mt-8 space-y-4">
            @foreach($profesores as $profesor)
                @php
                    $teamName = \App\Models\Setting::get('team_name', $profesor->nombre . ' ' . $profesor->apellido, $profesor->id);
                    $teamLogo = \App\Models\Setting::get('team_logo', null, $profesor->id);
                @endphp
                <form action="{{ route('grupos.set') }}" method="POST">
                    @csrf
                    <input type="hidden" name="profesor_id" value="{{ $profesor->id }}">
                    <button type="submit" class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="flex items-center">
                            @if($teamLogo)
                                <div class="w-10 h-10 bg-white p-1 rounded shadow-sm mr-3 flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('storage/' . $teamLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                </div>
                            @else
                                <div class="w-10 h-10 bg-blue-100 rounded mr-3 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-users"></i>
                                </div>
                            @endif
                            <div class="text-left">
                                <span class="block text-lg font-semibold text-gray-700 group-hover:text-blue-700">{{ $teamName }}</span>
                                <span class="block text-xs text-gray-500">{{ $profesor->nombre }} {{ $profesor->apellido }}</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
