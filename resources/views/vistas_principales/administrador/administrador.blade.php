<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Gestión de Inventario - Administrador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Control de Semillas</h3>
                    <p class="mt-2 text-gray-600">Aquí daremos de alta las nuevas semillas y variedades.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>