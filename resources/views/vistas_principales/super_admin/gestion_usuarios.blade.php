<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        {{-- 1. LLAMADA AL MENÚ LATERAL --}}
        <x-admin-sidebar />

        {{-- 2. CONTENIDO PRINCIPAL --}}
        <div class="flex-1">
            {{-- Alpine.js: Control total de los modales --}}
            <div x-data="{ 
                openCreate: false, 
                openEdit: false, 
                userToEdit: { id: '', name: '', email: '', role: '' } 
            }" x-cloak>
                
            

                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        
                        {{-- Alerta de éxito --}}
                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-b border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ strtoupper($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button @click="userToEdit = {{ $user }}; openEdit = true" class="text-indigo-600 hover:text-indigo-900 mr-4 font-bold">Editar</button>
                                            
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Eliminar usuario?')" class="text-red-600 hover:text-red-900 font-bold">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- --- MODAL EDITAR --- --}}
                <div x-show="openEdit" 
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-75 p-4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8" @click.away="openEdit = false">
                        <h3 class="text-2xl font-bold text-blue-800 mb-6">Editar Datos</h3>
                        <form :action="`/admin/usuarios/${userToEdit.id}`" method="POST" class="space-y-4">
                            @csrf @method('PATCH')
                            <input type="text" name="name" x-model="userToEdit.name" class="w-full rounded-lg border-gray-300">
                            <input type="email" name="email" x-model="userToEdit.email" class="w-full rounded-lg border-gray-300">
                            <select name="role" x-model="userToEdit.role" class="w-full rounded-lg border-gray-300">
                                <option value="encargado">Encargado</option>
                                <option value="administrador">Administrador</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <input type="password" name="password" placeholder="Nueva contraseña (opcional)" class="w-full rounded-lg border-gray-300">
                            
                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="openEdit = false" class="text-gray-500 hover:text-gray-700">Cerrar</button>
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- --- (Opcional) MODAL CREAR si lo quieres en esta misma vista --- --}}
                <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-75 p-4" x-cloak>
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8" @click.away="openCreate = false">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Nuevo Usuario</h3>
                        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="text" name="name" placeholder="Nombre completo" required class="w-full rounded-lg border-gray-300">
                            <input type="email" name="email" placeholder="Correo electrónico" required class="w-full rounded-lg border-gray-300">
                            <select name="role" class="w-full rounded-lg border-gray-300">
                                <option value="encargado">Encargado</option>
                                <option value="administrador">Administrador</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <input type="password" name="password" placeholder="Contraseña" required class="w-full rounded-lg border-gray-300">
                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="openCreate = false" class="text-gray-500">Cancelar</button>
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<style>
    [x-cloak] { display: none !important; }
</style>