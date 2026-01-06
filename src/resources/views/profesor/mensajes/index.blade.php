@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Mensajes']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mensajes</h1>
            <p class="text-gray-600">Conversaciones con tus alumnos</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="divide-y divide-gray-100">
            @forelse($alumnos as $alumno)
                <a href="{{ route('mensajes.show', $alumno->id) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                    <div class="relative">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl overflow-hidden border-2 border-white shadow-sm">
                            @if($alumno->image)
                                <img src="{{ asset('storage/' . $alumno->image) }}" alt="{{ $alumno->nombre }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($alumno->nombre, 0, 1) }}
                            @endif
                        </div>
                        @if($alumno->unread_count > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white">
                                {{ $alumno->unread_count }}
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-bold text-gray-900 truncate">{{ $alumno->nombre }} {{ $alumno->apellido }}</h3>
                            @if($alumno->last_message)
                                <span class="text-xs text-gray-500">
                                    {{ $alumno->last_message->created_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 truncate">
                            @if($alumno->last_message)
                                @if($alumno->last_message->sender_id === Auth::id())
                                    <span class="text-gray-400">Tú:</span>
                                @endif
                                @if($alumno->last_message->attachment_path)
                                    <span class="text-blue-600"><i class="fas fa-paperclip mr-1"></i> Archivo adjunto</span>
                                @else
                                    {{ $alumno->last_message->content }}
                                @endif
                            @else
                                <span class="text-gray-400 italic">Sin mensajes aún</span>
                            @endif
                        </p>
                    </div>

                    <div class="text-gray-300">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <div class="mb-4">
                        <i class="fas fa-comments text-5xl text-gray-200"></i>
                    </div>
                    No tienes alumnos asignados o conversaciones iniciadas.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
