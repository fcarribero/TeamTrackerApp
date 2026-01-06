@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Mensajes', 'url' => route('mensajes.index')],
        ['label' => 'Chat con ' . $otherUser->nombre]
    ]])
@endsection

@section('content')
<div class="h-[calc(100vh-12rem)] flex flex-col bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
    <!-- Header del Chat -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold overflow-hidden">
                @if($otherUser->image)
                    <img src="{{ asset('storage/' . $otherUser->image) }}" alt="{{ $otherUser->nombre }} {{ $otherUser->apellido }}" class="w-full h-full object-cover">
                @else
                    {{ substr($otherUser->nombre, 0, 1) }}
                @endif
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ $otherUser->nombre }} {{ $otherUser->apellido }}</h3>
                <p class="text-xs text-blue-600 flex items-center gap-1">
                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span> Profesor
                </p>
            </div>
        </div>
        <a href="{{ route('mensajes.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-chevron-left text-xl"></i>
        </a>
    </div>

    <!-- Mensajes -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-[#f0f2f5]">
        @forelse($messages as $message)
            <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] lg:max-w-[60%] rounded-2xl p-3 shadow-sm {{ $message->sender_id === Auth::id() ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-900 rounded-tl-none' }}">
                    @if($message->attachment_path)
                        <div class="mb-2">
                            @if($message->attachment_type === 'image')
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}" target="_blank">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}" class="rounded-lg max-h-64 object-cover">
                                </a>
                            @else
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}" target="_blank" class="flex items-center gap-2 p-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-blue-700' : 'bg-gray-100' }} hover:opacity-90 transition">
                                    <i class="fas fa-file-pdf text-2xl {{ $message->sender_id === Auth::id() ? 'text-blue-200' : 'text-red-500' }}"></i>
                                    <div class="text-xs truncate">
                                        <p class="font-bold truncate">{{ $message->attachment_name }}</p>
                                        <p class="{{ $message->sender_id === Auth::id() ? 'text-blue-200' : 'text-gray-500' }}">PDF</p>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($message->content)
                        <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                    @endif

                    <div class="flex items-center justify-end gap-1 mt-1">
                        <span class="text-[10px] {{ $message->sender_id === Auth::id() ? 'text-blue-100' : 'text-gray-400' }}">
                            {{ $message->created_at->format('H:i') }}
                        </span>
                        @if($message->sender_id === Auth::id())
                            <i class="fas fa-check-double text-[10px] {{ $message->is_read ? 'text-blue-200' : 'text-blue-400' }}"></i>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="h-full flex items-center justify-center">
                <div class="text-center text-gray-400">
                    <i class="fas fa-comments text-4xl mb-4 opacity-20"></i>
                    <p>Escríbele a tu profesor para cualquier duda</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Formulario de Envío -->
    <div class="p-4 bg-white border-t border-gray-100">
        <form action="{{ route('mensajes.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-3" x-data="{ fileName: '' }">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">

            <div class="flex-1 relative">
                <div x-show="fileName" class="absolute bottom-full left-0 mb-2 p-2 bg-blue-50 text-blue-700 text-xs rounded-lg flex items-center gap-2 border border-blue-100">
                    <i class="fas fa-file"></i>
                    <span x-text="fileName"></span>
                    <button type="button" @click="fileName = ''; $refs.fileInput.value = ''" class="text-blue-400 hover:text-blue-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <textarea
                    name="content"
                    rows="1"
                    placeholder="Escribe un mensaje..."
                    class="w-full bg-gray-100 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"
                ></textarea>
            </div>

            <div class="flex items-center gap-2 pb-1">
                <label class="cursor-pointer p-2 text-gray-400 hover:text-blue-600 transition">
                    <i class="fas fa-paperclip text-xl"></i>
                    <input type="file" name="attachment" class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0].name" accept=".jpg,.jpeg,.png,.pdf">
                </label>

                <button type="submit" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition shadow-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const chatContainer = document.getElementById('chat-messages');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // Auto-expand textarea
    const tx = document.getElementsByTagName('textarea');
    for (let i = 0; i < tx.length; i++) {
        tx[i].setAttribute('style', 'height:' + (tx[i].scrollHeight) + 'px;overflow-y:hidden;');
        tx[i].addEventListener("input", OnInput, false);
    }

    function OnInput() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    }
</script>
@endpush
@endsection
