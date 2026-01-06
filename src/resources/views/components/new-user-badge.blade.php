@if($user->isNewMember())
    <span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800']) }}>
        Nuevo
    </span>
@endif
