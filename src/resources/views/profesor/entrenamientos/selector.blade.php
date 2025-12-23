@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Seleccionar Plantilla</h1>
        <p class="text-gray-600">Elige una plantilla para tu entrenamiento o comienza desde cero</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Opción Sin Plantilla -->
        <a href="{{ route('entrenamientos.create', ['skip_plantilla' => 1]) }}"
           class="bg-white rounded-xl shadow-md p-6 border-2 border-transparent hover:border-blue-500 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 mb-4 group-hover:bg-blue-50 group-hover:text-blue-500 transition">
                    <i class="fas fa-plus-circle fa-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sin Plantilla</h3>
                <p class="text-gray-600 text-sm">Comienza un entrenamiento totalmente personalizado desde cero.</p>
            </div>
            <div class="mt-6 flex items-center text-blue-600 font-semibold gap-2">
                Continuar <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        @foreach($plantillas as $plantilla)
            <a href="{{ route('entrenamientos.create', ['plantilla_id' => $plantilla->id]) }}"
               class="bg-white rounded-xl shadow-md p-6 border-2 border-transparent hover:border-blue-500 transition group flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-100 transition">
                        <i class="fas fa-file-alt fa-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plantilla->nombre }}</h3>
                    <p class="text-gray-600 text-sm line-clamp-2">{{ $plantilla->descripcion ?? 'Sin descripción' }}</p>

                    <div class="mt-4 flex flex-wrap gap-1">
                        @if(is_array($plantilla->contenido))
                            @if(!empty($plantilla->contenido['calentamiento']))
                                <span class="bg-orange-100 text-orange-700 text-[10px] px-2 py-0.5 rounded-full">Calentamiento</span>
                            @endif
                            @if(!empty($plantilla->contenido['trabajo_principal']))
                                <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-0.5 rounded-full">Trabajo</span>
                            @endif
                            @if(!empty($plantilla->contenido['enfriamiento']))
                                <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full">Enfriamiento</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="mt-6 flex items-center text-blue-600 font-semibold gap-2">
                    Usar Plantilla <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        @endforeach
    </div>

    <div class="flex justify-center">
        <a href="{{ route('entrenamientos.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
            <i class="fas fa-times mr-2"></i> Cancelar
        </a>
    </div>
</div>
@endsection
