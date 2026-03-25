<div class="flex flex-col w-64 bg-white shadow-lg min-h-screen border-r border-gray-200">
    <div class="flex items-center justify-center h-16 border-b border-gray-100">
        <span class="text-blue-800 font-bold uppercase tracking-wider">Menú Principal</span>
    </div>
    
    <div class="flex flex-col flex-1 overflow-y-auto pt-4">
        <nav class="flex-1 px-4 space-y-2">
            
            {{-- Opción: Gestión de Usuarios (Activa) --}}
            <a href="{{ route('admin.users') }}" 
               class="flex items-center p-3 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.users*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-800' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Gestión Usuarios
            </a>

            {{-- Opción: Bitácora (Sin funcionalidad) --}}
            <div class="flex items-center p-3 text-sm font-semibold text-gray-400 cursor-not-allowed italic">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Bitácora Global
            </div>

            {{-- Opción: Ajustes (Sin funcionalidad) --}}
            <div class="flex items-center p-3 text-sm font-semibold text-gray-400 cursor-not-allowed italic">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                </svg>
                Ajustes
            </div>

        </nav>
    </div>
</div>