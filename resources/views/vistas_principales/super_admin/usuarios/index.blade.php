<x-app-layout>
    <style>
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes shine {
            0% { transform: translateX(-140%) skewX(-20deg); }
            100% { transform: translateX(220%) skewX(-20deg); }
        }

        .animate-fade-up {
            animation: fadeUp 0.8s ease-out forwards;
        }

        .btn-brand {
            background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(28, 96, 122, 0.15);
        }

        .btn-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(28, 96, 122, 0.25);
        }

        .btn-brand::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-20deg);
        }

        .btn-brand:hover::before {
            animation: shine 0.8s ease;
        }

        .input-brand {
            border: 1px solid #d9e4e8;
            transition: all 0.3s ease;
        }

        .input-brand:focus {
            border-color: #3bb49c;
            box-shadow: 0 0 0 4px rgba(59, 180, 156, 0.15);
            outline: none;
        }

        .glass-modal {
            backdrop-filter: blur(8px);
            background-color: rgba(15, 39, 46, 0.6);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="flex min-h-screen bg-[#f0f6f6] font-sans">
        <x-admin-sidebar />

        <div class="flex-1 w-full">
            <div
                x-data="{
                    openCreate: false,
                    openEdit: false,
                    userToEdit: { id: '', name: '', email: '', role: '' },
                    editAction: '',

                    loadEdit(data) {
                        this.userToEdit = {
                            id: data.id || '',
                            name: data.name || '',
                            email: data.email || '',
                            role: data.role || ''
                        };
                        this.editAction = data.action || '';
                        this.openEdit = true;
                    },

                    closeEdit() {
                        this.openEdit = false;
                        this.userToEdit = { id: '', name: '', email: '', role: '' };
                        this.editAction = '';
                    }
                }"
                x-cloak
                class="p-6 lg:p-10"
            >
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 animate-fade-up">
                    <div>
                        <h2 class="text-3xl font-extrabold text-[#1c607a]">Gestión de Investigadores</h2>
                        <p class="text-gray-500 mt-1">Administra los accesos y roles del sistema telemétrico.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate = true"
                        class="mt-4 sm:mt-0 btn-brand text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nuevo Usuario
                    </button>
                </div>

                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms
                         class="mb-6 p-4 bg-[#3bb49c]/10 border-l-4 border-[#3bb49c] rounded-r-lg flex justify-between items-center shadow-sm animate-fade-up">
                        <div class="flex items-center gap-3 text-[#1c607a] font-semibold">
                            <svg class="w-6 h-6 text-[#3bb49c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms
                         class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex justify-between items-center shadow-sm animate-fade-up">
                        <div class="flex items-center gap-3 text-red-700 font-semibold">
                            {{ session('error') }}
                        </div>
                        <button type="button" @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                            ✕
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm animate-fade-up">
                        <p class="font-bold text-red-700 mb-2">Corrige los siguientes errores:</p>
                        <ul class="list-disc pl-5 text-red-600 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-[0_15px_40px_-15px_rgba(28,96,122,0.15)] border border-gray-100 overflow-hidden animate-fade-up">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-[#1c607a]/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Investigador</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-[#1c607a] uppercase tracking-wider">Nivel de Acceso</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-[#1c607a] uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-50">
                            @forelse($users as $user)
                                <tr class="hover:bg-[#3bb49c]/5 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-[#1c607a] to-[#3bb49c] rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                        {{ $user->email }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($user->role === 'super_admin')
                                            <span class="px-4 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-[#1c607a] to-[#2f9aa0] text-white shadow-sm">
                                                    SUPER ADMIN
                                                </span>
                                        @elseif($user->role === 'administrador')
                                            <span class="px-4 py-1.5 inline-flex text-xs font-bold rounded-full bg-[#3bb49c]/20 text-[#1c607a] border border-[#3bb49c]/30">
                                                    ADMINISTRADOR
                                                </span>
                                        @else
                                            <span class="px-4 py-1.5 inline-flex text-xs font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                                    ENCARGADO
                                                </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <button
                                                type="button"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role }}"
                                                data-action="{{ route('super_admin.usuarios.update', $user) }}"
                                                @click="loadEdit($event.currentTarget.dataset)"
                                                class="text-[#3bb49c] hover:text-[#1c607a] bg-[#3bb49c]/10 hover:bg-[#3bb49c]/20 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>

                                            <form action="{{ route('super_admin.usuarios.destroy', $user) }}" method="POST" class="inline m-0">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    onclick="return confirm('¿Estás seguro de eliminar este investigador del sistema?')"
                                                    class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No hay usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- MODAL CREAR --}}
                <div x-show="openCreate"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center glass-modal p-4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
                         @click.away="openCreate = false"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <div class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] p-6 text-white text-center">
                            <h3 class="text-2xl font-extrabold">Nuevo Investigador</h3>
                            <p class="text-sm opacity-90 mt-1">Registra un nuevo usuario en el sistema.</p>
                        </div>

                        <form action="{{ route('super_admin.usuarios.store') }}" method="POST" class="p-8 space-y-5">
                            @csrf

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nombre completo</label>
                                <input type="text" name="name" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Correo electrónico</label>
                                <input type="email" name="email" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nivel de acceso</label>
                                <select name="role" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="encargado">Encargado</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Contraseña</label>
                                <input type="password" name="password" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">
                                    Registrar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL EDITAR --}}
                <div x-show="openEdit"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center glass-modal p-4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
                         @click.away="closeEdit()"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <div class="bg-[#f0f6f6] p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-extrabold text-[#1c607a]">Editar Datos</h3>
                                <p class="text-sm text-gray-500 mt-1">Modificando perfil de investigador.</p>
                            </div>
                            <div class="h-12 w-12 bg-white rounded-full flex items-center justify-center shadow-sm text-[#3bb49c]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </div>
                        </div>

                        <form :action="editAction" method="POST" class="p-8 space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nombre completo</label>
                                <input type="text" name="name" x-model="userToEdit.name" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Correo electrónico</label>
                                <input type="email" name="email" x-model="userToEdit.email" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nivel de acceso</label>
                                <select name="role" x-model="userToEdit.role" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="encargado">Encargado</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nueva contraseña (opcional)</label>
                                <input type="password" name="password" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                                <button type="button" @click="closeEdit()" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
