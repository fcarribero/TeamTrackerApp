<div class="hidden md:flex items-center gap-2 text-sm text-gray-500">
    @php
        $inicioUrl = Auth::user()->rol === 'profesor' ? route('dashboard.profesor') : route('dashboard.alumno');
        $isInicio = request()->url() === $inicioUrl;
    @endphp

    @if($isInicio)
        <span class="text-gray-900 font-medium">Inicio</span>
    @else
        <a href="{{ $inicioUrl }}" class="hover:text-blue-600 transition">Inicio</a>
    @endif

    @foreach($items ?? [] as $item)
        <i class="fas fa-chevron-right text-[10px]"></i>
        @if(!$loop->last)
            @if(isset($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-blue-600 transition">{{ $item['label'] }}</a>
            @else
                <span class="text-gray-500">{{ $item['label'] }}</span>
            @endif
        @else
            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</div>
