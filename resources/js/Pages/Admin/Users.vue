<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const roles = ref([]);
const users = ref([]);
const meta = ref(null);
const loading = ref(false);
const error = ref('');
const query = ref('');
const page = ref(1);

const form = reactive({
    id: null,
    nombre: '',
    email: '',
    rol_id: '',
    password: '',
    activo: true,
});

const formErrors = ref({});
const saving = ref(false);

const isEditing = computed(() => form.id !== null);

const resetForm = () => {
    form.id = null;
    form.nombre = '';
    form.email = '';
    form.rol_id = '';
    form.password = '';
    form.activo = true;
    formErrors.value = {};
};

const fetchRoles = async () => {
    const response = await window.axios.get('/api/admin/roles');
    roles.value = response?.data?.data ?? [];
};

const fetchUsers = async ({ page: requestedPage = 1, append = false } = {}) => {
    loading.value = true;
    error.value = '';

    try {
        const response = await window.axios.get('/api/admin/usuarios', {
            params: {
                q: query.value || undefined,
                per_page: 50,
                page: requestedPage,
            },
        });

        const items = response?.data?.data ?? [];
        users.value = append ? [...users.value, ...items] : items;
        meta.value = response?.data?.meta ?? null;
        page.value = meta.value?.current_page ?? requestedPage;
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudieron cargar los usuarios.';
    } finally {
        loading.value = false;
    }
};

const save = async () => {
    saving.value = true;
    formErrors.value = {};
    error.value = '';

    try {
        const payload = {
            nombre: form.nombre,
            email: form.email,
            rol_id: Number(form.rol_id),
        };

        if (!isEditing.value) {
            payload.password = form.password;
            const response = await window.axios.post('/api/admin/usuarios', payload);
            users.value = [response.data.data, ...users.value];
            resetForm();
            return;
        }

        if (form.password) {
            payload.password = form.password;
        }

        const response = await window.axios.patch(`/api/admin/usuarios/${form.id}`, payload);
        users.value = users.value.map((u) => (u.id === form.id ? response.data.data : u));
        resetForm();
    } catch (e) {
        const data = e?.response?.data;
        formErrors.value = data?.errors ?? {};
        error.value = data?.message || 'No se pudo guardar.';
    } finally {
        saving.value = false;
    }
};

const editUser = (user) => {
    formErrors.value = {};
    form.id = user.id;
    form.nombre = user.nombre;
    form.email = user.email;
    form.rol_id = user.rol_id;
    form.password = '';
    form.activo = Boolean(user.activo);
};

const toggleActive = async (user) => {
    saving.value = true;
    error.value = '';
    formErrors.value = {};

    try {
        const response = await window.axios.patch(`/api/admin/usuarios/${user.id}`, {
            activo: !user.activo,
        });
        users.value = users.value.map((u) => (u.id === user.id ? response.data.data : u));
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudo actualizar el usuario.';
        formErrors.value = data?.errors ?? {};
    } finally {
        saving.value = false;
    }
};

const loadMore = async () => {
    if (loading.value || !meta.value || meta.value.current_page >= meta.value.last_page) {
        return;
    }

    await fetchUsers({ page: page.value + 1, append: true });
};

onMounted(async () => {
    await Promise.all([fetchRoles(), fetchUsers()]);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Administracion - Usuarios" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administracion</h2>
                <Link class="text-sm text-indigo-600 hover:text-indigo-700" :href="route('admin.catalogs')">
                    Catalogos
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <section class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">Usuarios</h3>
                        <div class="flex items-center gap-2">
                            <TextInput v-model="query" type="text" class="w-64" placeholder="Buscar..." />
                            <SecondaryButton :disabled="loading" @click="fetchUsers">Buscar</SecondaryButton>
                        </div>
                    </div>

                    <InputError v-if="error" class="mt-4" :message="error" />

                    <div v-if="loading && users.length === 0" class="mt-4 text-sm text-gray-500">Cargando...</div>
                    <div v-else-if="users.length === 0" class="mt-4 text-sm text-gray-500">Sin usuarios.</div>

                    <div v-else class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700">Nombre</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700">Email</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700">Rol</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700">Activo</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="user in users" :key="user.id">
                                    <td class="px-3 py-2">{{ user.nombre }}</td>
                                    <td class="px-3 py-2">{{ user.email }}</td>
                                    <td class="px-3 py-2">{{ user.rol?.nombre || user.rol_id }}</td>
                                    <td class="px-3 py-2">
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="user.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                                        >
                                            {{ user.activo ? 'Si' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right space-x-2">
                                        <SecondaryButton @click="editUser(user)">Editar</SecondaryButton>
                                        <DangerButton v-if="user.activo" :disabled="saving" @click="toggleActive(user)">
                                            Desactivar
                                        </DangerButton>
                                        <PrimaryButton v-else :disabled="saving" @click="toggleActive(user)">
                                            Activar
                                        </PrimaryButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="meta && meta.current_page < meta.last_page" class="mt-4">
                            <SecondaryButton :disabled="loading" @click="loadMore">Cargar mas</SecondaryButton>
                        </div>
                    </div>
                </section>

                <section class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Editar usuario' : 'Crear usuario' }}
                        </h3>
                        <SecondaryButton type="button" @click="resetForm">Limpiar</SecondaryButton>
                    </div>

                    <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="save">
                        <div>
                            <InputLabel for="nombre" value="Nombre" />
                            <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="formErrors.nombre?.[0]" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="formErrors.email?.[0]" />
                        </div>

                        <div>
                            <InputLabel for="rol" value="Rol" />
                            <select
                                id="rol"
                                v-model="form.rol_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                            >
                                <option value="" disabled>Selecciona un rol</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                    {{ role.nombre }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="formErrors.rol_id?.[0]" />
                        </div>

                        <div>
                            <InputLabel for="password" :value="isEditing ? 'Password (opcional)' : 'Password'" />
                            <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="formErrors.password?.[0]" />
                        </div>

                        <div class="md:col-span-2">
                            <PrimaryButton :disabled="saving">
                                {{ isEditing ? 'Guardar cambios' : 'Crear usuario' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

