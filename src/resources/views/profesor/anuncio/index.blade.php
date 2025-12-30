@extends('layouts.dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <style>
        .ql-editor {
            min-height: 150px;
            font-size: 1rem;
        }
    </style>
@endpush

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Anuncio Global']]])
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Anuncio para Alumnos</h1>
            <p class="text-gray-600">Este anuncio aparecerá en la parte superior del panel de los alumnos cuando ingresen al sistema.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Publicar Anuncio</h2>
            <form action="{{ route('anuncios.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-1">Contenido del Anuncio</label>
                    <div id="editor-container" class="bg-white">
                        {!! $anuncio ? $anuncio->contenido : '' !!}
                    </div>
                    <input type="hidden" name="contenido" id="contenido">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" id="activo" value="1" {{ (!$anuncio || $anuncio->activo) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="activo" class="text-sm text-gray-700">Mostrar anuncio inmediatamente</label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-bullhorn"></i>
                        {{ $anuncio ? 'Actualizar Anuncio' : 'Publicar Anuncio' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Previsualización -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Estado Actual</h2>
                @if($anuncio)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $anuncio->activo ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-700 border border-gray-100' }}">
                            <div class="flex items-center gap-2">
                                <i class="fas {{ $anuncio->activo ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                <span class="font-medium">{{ $anuncio->activo ? 'Visible para alumnos' : 'Oculto' }}</span>
                            </div>
                            <form action="{{ route('anuncios.toggle', $anuncio->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm underline font-semibold">
                                    {{ $anuncio->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>

                        <div class="mb-6 bg-blue-600 text-white p-4 rounded-xl shadow-lg flex items-start gap-4 animate-fade-in-down">
                            <div class="bg-white/20 p-2 rounded-lg">
                                <i class="fas fa-bullhorn text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-lg mb-1">Aviso Importante</h4>
                                <div class="text-blue-50 leading-relaxed anuncio-contenido">
                                    {!! strip_tags($anuncio->contenido, '<b><strong><i><em><u><ul><ol><li><p><br>') !!}
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            Última actualización: {{ $anuncio->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-bullhorn text-4xl mb-3 opacity-20"></i>
                        <p>No hay ningún anuncio configurado todavía.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                },
                placeholder: 'Escribe el mensaje para los alumnos aquí...'
            });

            // Al enviar el formulario, copiar el contenido del editor al input hidden
            var form = document.querySelector('form[action="{{ route('anuncios.store') }}"]');
            form.onsubmit = function() {
                var contenido = document.querySelector('#contenido');
                // Obtener el HTML del editor
                contenido.value = quill.root.innerHTML;

                // Si el editor está vacío (solo un párrafo vacío), limpiar el valor para que 'required' funcione
                if (quill.getText().trim().length === 0 && quill.root.innerHTML.indexOf('<img') === -1) {
                    contenido.value = '';
                }
            };
        });
    </script>
@endpush
