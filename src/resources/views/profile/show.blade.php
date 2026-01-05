@extends('layouts.dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Mi Perfil']]])
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
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
                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl mx-auto mb-4 border-4 border-white shadow-md">
                    {{ substr($user->nombre, 0, 1) }}
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
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-all text-sm">
                                Guardar Cambios
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

                        <div class="pt-2">
                            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                                <i class="fas fa-shield-alt"></i> Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
