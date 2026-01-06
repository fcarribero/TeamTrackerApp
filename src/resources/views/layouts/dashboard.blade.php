@php
    $user = Auth::user();
    $settingsUserId = $user->id;
    if ($user->rol === 'alumno') {
        $settingsUserId = session('active_profesor_id');
        if (!$settingsUserId) {
            // Fallback por si no hay sesión pero sí un profesor
            $settingsUserId = $user->grupos()->pluck('profesorId')->first();
        }
        $settingsUserId = $settingsUserId ?: $user->id;
    }
    $teamLogo = \App\Models\Setting::get('team_logo', null, $settingsUserId);
    $teamName = \App\Models\Setting::get('team_name', null, $settingsUserId);

    $pagosPendientesCount = 0;
    if ($user->rol === 'alumno') {
        $pagosPendientesCount = $user->pagos()->whereIn('estado', ['pendiente', 'vencido'])->count();
    } elseif ($user->rol === 'profesor') {
        $pagosPendientesCount = \App\Models\Pago::where('profesorId', $user->id)
            ->whereIn('estado', ['pendiente', 'vencido'])
            ->count();
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($teamName ? $teamName . ' | ' : '') . ($title ?? 'TeamTracker') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        .anuncio-contenido p:last-child { margin-bottom: 0; }
        .anuncio-contenido ul { list-style-type: disc; margin-left: 1.5rem; }
        .anuncio-contenido ol { list-style-type: decimal; margin-left: 1.5rem; }
    </style>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-blue-600 to-blue-700 text-white transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 shadow-xl overflow-y-auto">

            <div class="p-6 border-b border-white/20">
                <div class="flex items-center gap-3">
                    @if($teamLogo)
                        <div class="bg-white p-1 rounded-xl shadow-lg w-12 h-12 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('storage/' . $teamLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        </div>
                    @else
                        <div class="bg-white p-2 rounded-xl shadow-lg">
                            <i class="fas fa-dumbbell text-blue-600 text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="font-bold text-xl">TeamTracker</h2>
                        @if($teamName)
                            <p class="text-xs text-blue-100 font-bold tracking-wider uppercase opacity-90">{{ $teamName }}</p>
                        @else
                            <p class="text-xs text-blue-200 capitalize font-medium">{{ Auth::user()->rol }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 border-b border-white/20">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-lg overflow-hidden shrink-0 border border-white/10">
                            @if(Auth::user()->image)
                                <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="{{ Auth::user()->nombre }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->nombre, 0, 1) }}
                            @endif
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold truncate">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
                            </div>
                            @if(Auth::user()->rol === 'alumno')
                                <p class="text-xs text-blue-200 truncate">DNI: {{ Auth::user()->dni ?? '-' }}</p>
                            @else
                                <p class="text-xs text-blue-200 truncate">{{ Auth::user()->email }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider mb-3 px-3">Menú Principal</p>

                @if(Auth::user()->rol === 'profesor')
                    <a id="tour-profesor-dashboard" href="{{ route('dashboard.profesor') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard.profesor') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-home w-5 text-center"></i> <span class="font-medium">Dashboard</span>
                    </a>
                    <a id="tour-profesor-alumnos" href="/dashboard/profesor/alumnos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/alumnos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-users w-5 text-center"></i> <span class="font-medium">Alumnos</span>
                    </a>
                    <a id="tour-profesor-grupos" href="/dashboard/profesor/grupos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/grupos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-clipboard-list w-5 text-center"></i> <span class="font-medium">Grupos</span>
                    </a>
                    <a id="tour-profesor-pagos" href="/dashboard/profesor/pagos" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/pagos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card w-5 text-center"></i> <span class="font-medium">Pagos</span>
                        </div>
                        @if($pagosPendientesCount > 0)
                            <span class="flex h-2 w-2 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.6)]"></span>
                        @endif
                    </a>
                    <a id="tour-profesor-entrenamientos" href="/dashboard/profesor/entrenamientos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/entrenamientos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-calendar w-5 text-center"></i> <span class="font-medium">Entrenamientos</span>
                    </a>
                    <a id="tour-profesor-plantillas" href="/dashboard/profesor/plantillas" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/plantillas*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-file-alt w-5 text-center"></i> <span class="font-medium">Plantillas</span>
                    </a>
                    <a id="tour-profesor-competencias" href="{{ route('competencias.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('competencias.*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-medal w-5 text-center"></i> <span class="font-medium">Competencias</span>
                    </a>
                    <a id="tour-profesor-anuncios" href="{{ route('anuncios.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('anuncios.index') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-bullhorn w-5 text-center"></i> <span class="font-medium">Anuncio</span>
                    </a>
                    <a id="tour-profesor-settings" href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('settings.index') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-cog w-5 text-center"></i> <span class="font-medium">Configuración</span>
                    </a>
                @else
                    <a id="tour-entrenamientos" href="/dashboard/alumno/entrenamientos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/alumno/entrenamientos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-calendar w-5 text-center"></i> <span class="font-medium">Mis Entrenamientos</span>
                    </a>
                    <a id="tour-pagos" href="/dashboard/alumno/pagos" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/alumno/pagos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card w-5 text-center"></i> <span class="font-medium">Mis Pagos</span>
                        </div>
                        @if($pagosPendientesCount > 0)
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold bg-amber-400 text-blue-900 px-1.5 py-0.5 rounded-md shadow-sm">
                                    {{ $pagosPendientesCount }}
                                </span>
                            </div>
                        @endif
                    </a>
                    <a id="tour-competencias" href="{{ route('alumno.competencias') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('alumno.competencias') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-medal w-5 text-center"></i> <span class="font-medium">Mis Competencias</span>
                    </a>
                    <a id="tour-configuracion" href="{{ route('alumno.configuracion') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('alumno.configuracion') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-cog w-5 text-center"></i> <span class="font-medium">Configuración</span>
                    </a>
                @endif

                <a id="tour-perfil" href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('profile.show') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                    <i class="fas fa-user w-5 text-center"></i> <span class="font-medium">Mi Perfil</span>
                </a>

            </nav>

            <div class="p-4 border-t border-white/20">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-100 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-sign-out-alt"></i> <span class="font-medium">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Navbar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 lg:px-6 h-16 flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex items-center">
                        @yield('breadcrumbs')
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto p-4 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
    @if(Auth::user()->rol === 'alumno')
        <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const driver = window.driver.js.driver;

                const driverObj = driver({
                    showProgress: true,
                    nextBtnText: 'Siguiente',
                    prevBtnText: 'Anterior',
                    doneBtnText: 'Finalizar',
                    steps: [
                        // Pasos del Sidebar (Siempre presentes)
                        { element: '#tour-entrenamientos', popover: { title: 'Tus Entrenamientos', description: 'Aquí podrás ver tu plan de entrenamiento diario y completar tus devoluciones.', side: "right", align: 'start' }},
                        { element: '#tour-pagos', popover: { title: 'Tus Pagos', description: 'Consulta el estado de tus cuotas y el historial de pagos realizados.', side: "right", align: 'start' }},
                        { element: '#tour-competencias', popover: { title: 'Tus Competencias', description: 'Registra tus próximas carreras y carga tus planes de carrera.', side: "right", align: 'start' }},
                        { element: '#tour-configuracion', popover: { title: 'Configuración', description: 'Conecta tu cuenta de Garmin y ajusta tu ubicación para el clima.', side: "right", align: 'start' }},
                        { element: '#tour-perfil', popover: { title: 'Mi Perfil', description: 'Gestiona tus datos personales, obra social y certificado médico.', side: "right", align: 'start' }},

                        // Pasos condicionales según la página actual
                        ...(document.querySelector('#tour-proximos-entrenamientos') ? [
                            { element: '#tour-proximos-entrenamientos', popover: { title: 'Próximas Sesiones', description: 'Aquí verás lo que tienes programado para hoy y los próximos días.', side: "top" }},
                            { element: '#tour-historial-entrenamientos', popover: { title: 'Tu Historial', description: 'Puedes revisar tus entrenamientos pasados y ver tus devoluciones.', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-tabla-pagos') ? [
                            { element: '#tour-tabla-pagos', popover: { title: 'Historial de Pagos', description: 'Detalle de cada mensualidad, monto y estado (Pagado/Pendiente/Vencido).', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-nueva-competencia') ? [
                            { element: '#tour-nueva-competencia', popover: { title: 'Anota tu Carrera', description: 'Informa al profesor sobre tus próximos objetivos para que pueda planificar tu entrenamiento.', side: "right" }},
                            { element: '#tour-lista-competencias', popover: { title: 'Tus Inscripciones', description: 'Aquí aparecerán todas tus competencias y los planes de carrera que el profesor te asigne.', side: "left" }}
                        ] : [])
                    ]
                });

                window.startTour = () => driverObj.drive();

                if (!localStorage.getItem('alumno_tour_completed')) {
                    driverObj.drive();
                    localStorage.setItem('alumno_tour_completed', 'true');
                }
            });
        </script>
    @endif

    @if(Auth::user()->rol === 'profesor')
        <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const driver = window.driver.js.driver;

                const driverObj = driver({
                    showProgress: true,
                    nextBtnText: 'Siguiente',
                    prevBtnText: 'Anterior',
                    doneBtnText: 'Finalizar',
                    steps: [
                        // Sidebar
                        { element: '#tour-profesor-dashboard', popover: { title: 'Dashboard', description: 'Resumen general de tu equipo, alumnos y entrenamientos.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-alumnos', popover: { title: 'Gestión de Alumnos', description: 'Aquí puedes invitar nuevos alumnos y ver sus perfiles completos.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-grupos', popover: { title: 'Grupos', description: 'Organiza a tus alumnos en grupos para facilitar la asignación de entrenamientos.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-pagos', popover: { title: 'Pagos y Cobros', description: 'Lleva el control de las mensualidades e ingresos de tus alumnos.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-entrenamientos', popover: { title: 'Calendario', description: 'Programa sesiones de entrenamiento y revisa las devoluciones de tus alumnos.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-plantillas', popover: { title: 'Plantillas', description: 'Crea modelos de entrenamiento reutilizables para ahorrar tiempo.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-competencias', popover: { title: 'Competencias', description: 'Sigue los objetivos de tus alumnos y crea sus planes de carrera.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-anuncios', popover: { title: 'Anuncios', description: 'Publica avisos importantes que todos tus alumnos verán al ingresar.', side: "right", align: 'start' }},
                        { element: '#tour-profesor-settings', popover: { title: 'Configuración de Equipo', description: 'Personaliza el nombre y logo de tu equipo, y ajusta las preferencias.', side: "right", align: 'start' }},

                        // Contextuales
                        ...(document.querySelector('#tour-stats-cards') ? [
                            { element: '#tour-stats-cards', popover: { title: 'Estadísticas Rápidas', description: 'Vistazo rápido a los números clave de tu equipo este mes.', side: "top" }},
                            { element: '#tour-proximos-entrenamientos', popover: { title: 'Entrenamientos Cercanos', description: 'Lo que tienes programado para los próximos días.', side: "top" }},
                            { element: '#tour-ultimos-alumnos', popover: { title: 'Nuevas Incorporaciones', description: 'Los últimos alumnos que se han unido a tu equipo.', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-invitar-alumno') ? [
                            { element: '#tour-invitar-alumno', popover: { title: 'Hacer crecer tu equipo', description: 'Envía una invitación por correo para que un alumno se registre.', side: "top" }},
                            { element: '#tour-lista-alumnos', popover: { title: 'Tu lista de Alumnos', description: 'Accede al perfil, notas y historial de cada uno de tus alumnos.', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-lista-grupos') ? [
                            { element: '#tour-lista-grupos', popover: { title: 'Tus Grupos', description: 'Visualiza y gestiona los grupos de entrenamiento que has creado.', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-pagos-stats') ? [
                            { element: '#tour-pagos-stats', popover: { title: 'Resumen Financiero', description: 'Ingresos del mes y alertas sobre pagos vencidos.', side: "top" }},
                            { element: '#tour-lista-pagos', popover: { title: 'Historial de Cobros', description: 'Registro detallado de todos los pagos realizados por tus alumnos.', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-lista-entrenamientos') ? [
                            { element: '#tour-lista-entrenamientos', popover: { title: 'Historial de Sesiones', description: 'Revisa entrenamientos pasados y accede a las devoluciones de los alumnos.', side: "top" }}
                        ] : []),

                        ...(document.querySelector('#tour-lista-competencias') ? [
                            { element: '#tour-lista-competencias', popover: { title: 'Objetivos del Equipo', description: 'Mira las próximas carreras de tus alumnos y carga sus planes de carrera específicos.', side: "top" }}
                        ] : [])
                    ]
                });

                window.startProfesorTour = () => driverObj.drive();

                if (!localStorage.getItem('profesor_tour_completed')) {
                    driverObj.drive();
                    localStorage.setItem('profesor_tour_completed', 'true');
                }
            });
        </script>
    @endif
</body>
</html>
