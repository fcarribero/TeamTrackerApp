<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard - TeamTracker' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-blue-600 to-blue-700 text-white transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 shadow-xl overflow-y-auto">

            <div class="p-6 border-b border-white/20">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-xl shadow-lg">
                        <i class="fas fa-dumbbell text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl">TeamTracker</h2>
                        <p class="text-xs text-blue-200 capitalize font-medium">{{ Auth::user()->rol }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 border-b border-white/20">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-blue-200 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider mb-3 px-3">Menú Principal</p>

                @if(Auth::user()->rol === 'profesor')
                    <a href="{{ route('dashboard.profesor') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard.profesor') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-home w-5 text-center"></i> <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="/dashboard/profesor/alumnos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/alumnos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-users w-5 text-center"></i> <span class="font-medium">Alumnos</span>
                    </a>
                    <a href="/dashboard/profesor/grupos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/grupos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-clipboard-list w-5 text-center"></i> <span class="font-medium">Grupos</span>
                    </a>
                    <a href="/dashboard/profesor/pagos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/pagos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-credit-card w-5 text-center"></i> <span class="font-medium">Pagos</span>
                    </a>
                    <a href="/dashboard/profesor/entrenamientos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/entrenamientos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-calendar w-5 text-center"></i> <span class="font-medium">Entrenamientos</span>
                    </a>
                    <a href="/dashboard/profesor/plantillas" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/profesor/plantillas*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-file-alt w-5 text-center"></i> <span class="font-medium">Plantillas</span>
                    </a>
                @else
                    <a href="{{ route('dashboard.alumno') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard.alumno') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-home w-5 text-center"></i> <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="/dashboard/alumno/entrenamientos" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard/alumno/entrenamientos*') ? 'bg-white text-blue-600 shadow-lg' : 'text-blue-50 hover:bg-white/10' }}">
                        <i class="fas fa-calendar w-5 text-center"></i> <span class="font-medium">Mis Entrenamientos</span>
                    </a>
                @endif
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

                    <div class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                        <span>Inicio</span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                        <span class="text-gray-900 font-medium">Dashboard</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="h-8 w-[1px] bg-gray-200"></div>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->rol }}</p>
                            </div>
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto p-4 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
