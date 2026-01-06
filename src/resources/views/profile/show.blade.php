@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Mi Perfil']]])
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" />
    <style>
        .croppie-container .cr-viewport {
            border: 4px solid #fff;
            box-shadow: 0 0 0 2000px rgba(0,0,0,0.5);
        }
        .cr-boundary {
            border-radius: 0.75rem;
        }
    </style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="profileImageCropper()">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-500"></i>
            Mi Perfil
        </h1>
        <p class="text-gray-600 mt-1">Visualiza tu información y gestiona tu seguridad</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Perfil -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 text-center">
                <div class="relative inline-block">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl mx-auto mb-4 border-4 border-white shadow-md overflow-hidden">
                        @if($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->nombre }} {{ $user->apellido }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($user->nombre, 0, 1) }}
                        @endif
                    </div>
                </div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $user->nombre }} {{ $user->apellido }}
                </h2>
                <p class="text-sm text-blue-600 font-medium uppercase tracking-wider">{{ $user->rol }}</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Miembro desde</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            @if($user->rol === 'alumno' && $user->profesores->count() > 0)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="p-4 bg-gray-50 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-users text-blue-500"></i>
                            Mis Equipos
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @foreach($user->profesores as $profesor)
                            @php
                                $teamLogo = \App\Models\Setting::get('team_logo', null, $profesor->id);
                                $teamName = \App\Models\Setting::get('team_name', null, $profesor->id);
                            @endphp
                            <div class="flex items-center gap-3">
                                @if($teamLogo)
                                    <div class="w-10 h-10 bg-white p-1 rounded-lg border border-gray-100 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('storage/' . $teamLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                        <i class="fas fa-users text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $teamName ?: $profesor->nombre . ' ' . $profesor->apellido }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $profesor->email }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Información Detallada -->
        <div class="md:col-span-2 space-y-6">
            <!-- Información Básica -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Información Personal
                    </h3>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Imagen de Perfil -->
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6">
                            <label class="block text-sm font-bold text-blue-900 mb-2">
                                <i class="fas fa-camera mr-2"></i> Foto de Perfil
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="shrink-0">
                                    <template x-if="!croppedImage">
                                        @if($user->image)
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="Preview" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm">
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold">
                                                {{ substr($user->nombre, 0, 1) }}
                                            </div>
                                        @endif
                                    </template>
                                    <template x-if="croppedImage">
                                        <img :src="croppedImage" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm">
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="image_input" accept="image/*" @change="onFileChange"
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                    <input type="hidden" name="image_base64" x-model="croppedImage">
                                    <p class="text-xs text-blue-600 mt-1">Recomendado: Cuadrada, JPG o PNG. Máx 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Apellido</label>
                                <input type="text" name="apellido" value="{{ old('apellido', $user->apellido) }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Correo Electrónico (No modificable)</label>
                                <input type="email" value="{{ $user->email }}" readonly
                                       class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-500 text-sm cursor-not-allowed">
                            </div>

                            @if($user->rol === 'alumno')
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">DNI</label>
                                    <input type="text" name="dni" value="{{ old('dni', $user->dni) }}" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Fecha de Nacimiento</label>
                                    <input type="date" name="fechaNacimiento" value="{{ old('fechaNacimiento', $user->fechaNacimiento ? $user->fechaNacimiento->format('Y-m-d') : '') }}" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sexo</label>
                                    <select name="sexo" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="masculino" {{ old('sexo', $user->sexo) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="femenino" {{ old('sexo', $user->sexo) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Obra Social</label>
                                        <input type="text" name="obra_social" value="{{ old('obra_social', $user->obra_social) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Número de Socio</label>
                                        <input type="text" name="numero_socio" value="{{ old('numero_socio', $user->numero_socio) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 border-t border-gray-100 pt-4 mt-2">
                                    <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="fas fa-file-medical text-blue-500"></i>
                                        Certificado Médico
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Archivo de Certificado</label>
                                            <input type="file" name="certificado_medico" accept=".pdf,.jpg,.jpeg,.png"
                                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            @if($user->certificado_medico)
                                                <div class="flex items-center gap-2 mt-2 text-xs text-blue-600">
                                                    <i class="fas fa-check-circle"></i>
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($user->certificado_medico) }}" target="_blank" class="hover:underline font-bold">Ver actual</a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Vencimiento</label>
                                            <input type="date" name="vencimiento_certificado" value="{{ old('vencimiento_certificado', $user->vencimiento_certificado ? $user->vencimiento_certificado->format('Y-m-d') : '') }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                                <i class="fas fa-save mr-2"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Actualizar Contraseña -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-lock text-blue-500"></i>
                        Seguridad
                    </h3>

                    <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                <input type="password" name="password" id="password" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                                <i class="fas fa-save mr-2"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recorte -->
    <div x-show="showModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-crop-alt text-blue-500"></i>
                                Ajustar Foto de Perfil
                            </h3>
                            <div class="mt-2 w-full">
                                <div id="croppie-container" class="w-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" @click="cropImage" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Confirmar Recorte
                    </button>
                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
    <script>
        function profileImageCropper() {
            return {
                showModal: false,
                croppedImage: null,
                croppieInstance: null,

                onFileChange(e) {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            this.initCroppie(event.target.result);
                        };
                        reader.readAsDataURL(files[0]);
                    }
                },

                initCroppie(imageSrc) {
                    this.showModal = true;
                    this.$nextTick(() => {
                        const el = document.getElementById('croppie-container');
                        if (this.croppieInstance) {
                            this.croppieInstance.destroy();
                        }
                        this.croppieInstance = new Croppie(el, {
                            viewport: { width: 200, height: 200, type: 'circle' },
                            boundary: { width: 300, height: 300 },
                            showZoomer: true,
                            enableOrientation: true
                        });
                        this.croppieInstance.bind({
                            url: imageSrc
                        });
                    });
                },

                cropImage() {
                    this.croppieInstance.result({
                        type: 'base64',
                        size: 'viewport',
                        format: 'jpeg',
                        quality: 0.9
                    }).then((base64) => {
                        this.croppedImage = base64;
                        this.showModal = false;
                        // Reset file input to allow choosing the same file again if needed
                        document.getElementById('image_input').value = '';
                    });
                }
            }
        }
    </script>
@endpush
