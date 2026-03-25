<x-app-layout>
    <div class="flex">
        {{-- Llamamos al componente que creamos --}}
        <x-admin-sidebar />

        {{-- Contenido Principal --}}
        <div class="flex-1">


            <div class="py-12 px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border-t-4 border-blue-500">
                    <h3 class="text-2xl font-bold italic text-blue-900 mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 mb-8">Selecciona una opción del menú lateral para gestionar el sistema.</p>
                    
                    {{-- Aquí puedes dejar las tarjetas originales o quitarlas si prefieres solo el Sidebar --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 bg-blue-50 border border-blue-100 rounded-xl">
                            <span class="text-blue-800 font-bold uppercase text-xs">Estado del Sistema</span>
                            <p class="text-gray-700 mt-2">Actualmente tienes acceso completo como Super Administrador.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>