@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Mensajes']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mensajes</h1>
            <p class="text-gray-600">Selecciona un profesor para chatear</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($profesores as $profesor)
            <a href="{{ route('mensajes.show', $profesor->id) }}" class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-2xl overflow-hidden border-4 border-white shadow-sm">
                    @if($profesor->image)
                        <img src="{{ asset('storage/' . $profesor->image) }}" alt="{{ $profesor->nombre }}" class="w-full h-full object-cover">
                    @else
                        {{ substr($profesor->nombre, 0, 1) }}
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">{{ $profesor->nombre }} {{ $profesor->apellido }}</h3>
                    <p class="text-sm text-gray-500">Profesor</p>
                    @php
                        $unreadCount = \App\Models\Message::where('sender_id', $profesor->id)
                            ->where('receiver_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="inline-block mt-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full">
                            {{ $unreadCount }} mensajes nuevos
                        </span>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 bg-white rounded-xl shadow-md border border-gray-100">
                <div class="mb-4">
                    <i class="fas fa-user-slash text-5xl text-gray-200"></i>
                </div>
                No tienes profesores asignados actualmente.
            </div>
        @endforelse
    </div>
</div>
@endsection
