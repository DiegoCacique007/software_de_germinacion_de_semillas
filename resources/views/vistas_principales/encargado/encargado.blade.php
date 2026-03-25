<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-yellow-700 leading-tight">
            {{ __('Registro de Actividades - Encargado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-yellow-400">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Bitácora Diaria</h3>
                    <p class="mt-2 text-gray-600">Registra el riego y el estado de germinación de hoy.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>