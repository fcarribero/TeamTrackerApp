@props(['grupo'])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div style="background-color: {{ $grupo->color ?? '#3B82F6' }};" class="w-2.5 h-2.5 rounded-full shrink-0"></div>
    <span class="text-xs font-medium text-gray-700 truncate">{{ $grupo->nombre }}</span>
</div>
