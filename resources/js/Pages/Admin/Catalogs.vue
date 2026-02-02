<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const loading = ref(false);
const error = ref('');

const sistemas = ref([]);
const prioridades = ref([]);
const tiposSolicitud = ref([]);

const sistemaForm = reactive({ id: null, nombre: '', activo: true });
const prioridadForm = reactive({ id: null, nombre: '', orden: 0, activo: true });
const tipoForm = reactive({ id: null, nombre: '', activo: true });

const formErrors = ref({});

const resetErrors = () => {
    formErrors.value = {};
    error.value = '';
};

const fetchAll = async () => {
    loading.value = true;
    error.value = '';

    try {
        const [s, p, t] = await Promise.all([
            window.axios.get('/api/admin/catalogos/sistemas'),
            window.axios.get('/api/admin/catalogos/prioridades'),
            window.axios.get('/api/admin/catalogos/tipos-solicitud'),
        ]);

        sistemas.value = s?.data?.data ?? [];
        prioridades.value = p?.data?.data ?? [];
        tiposSolicitud.value = t?.data?.data ?? [];
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudieron cargar los catalogos.';
    } finally {
        loading.value = false;
    }
};

const saveSistema = async () => {
    resetErrors();
    loading.value = true;

    try {
        if (!sistemaForm.id) {
            const response = await window.axios.post('/api/admin/catalogos/sistemas', {
                nombre: sistemaForm.nombre,
                activo: sistemaForm.activo,
            });
            sistemas.value = [response.data.data, ...sistemas.value];
        } else {
            const response = await window.axios.patch(`/api/admin/catalogos/sistemas/${sistemaForm.id}`, {
                nombre: sistemaForm.nombre,
                activo: sistemaForm.activo,
            });
            sistemas.value = sistemas.value.map((x) => (x.id === sistemaForm.id ? response.data.data : x));
        }

        sistemaForm.id = null;
        sistemaForm.nombre = '';
        sistemaForm.activo = true;
    } catch (e) {
        const data = e?.response?.data;
        formErrors.value = data?.errors ?? {};
        error.value = data?.message || 'No se pudo guardar el sistema.';
    } finally {
        loading.value = false;
    }
};

const savePrioridad = async () => {
    resetErrors();
    loading.value = true;

    try {
        if (!prioridadForm.id) {
            const response = await window.axios.post('/api/admin/catalogos/prioridades', {
                nombre: prioridadForm.nombre,
                orden: Number(prioridadForm.orden),
                activo: prioridadForm.activo,
            });
            prioridades.value = [response.data.data, ...prioridades.value];
        } else {
            const response = await window.axios.patch(`/api/admin/catalogos/prioridades/${prioridadForm.id}`, {
                nombre: prioridadForm.nombre,
                orden: Number(prioridadForm.orden),
                activo: prioridadForm.activo,
            });
            prioridades.value = prioridades.value.map((x) => (x.id === prioridadForm.id ? response.data.data : x));
        }

        prioridadForm.id = null;
        prioridadForm.nombre = '';
        prioridadForm.orden = 0;
        prioridadForm.activo = true;
    } catch (e) {
        const data = e?.response?.data;
        formErrors.value = data?.errors ?? {};
        error.value = data?.message || 'No se pudo guardar la prioridad.';
    } finally {
        loading.value = false;
    }
};

const saveTipo = async () => {
    resetErrors();
    loading.value = true;

    try {
        if (!tipoForm.id) {
            const response = await window.axios.post('/api/admin/catalogos/tipos-solicitud', {
                nombre: tipoForm.nombre,
                activo: tipoForm.activo,
            });
            tiposSolicitud.value = [response.data.data, ...tiposSolicitud.value];
        } else {
            const response = await window.axios.patch(`/api/admin/catalogos/tipos-solicitud/${tipoForm.id}`, {
                nombre: tipoForm.nombre,
                activo: tipoForm.activo,
            });
            tiposSolicitud.value = tiposSolicitud.value.map((x) => (x.id === tipoForm.id ? response.data.data : x));
        }

        tipoForm.id = null;
        tipoForm.nombre = '';
        tipoForm.activo = true;
    } catch (e) {
        const data = e?.response?.data;
        formErrors.value = data?.errors ?? {};
        error.value = data?.message || 'No se pudo guardar el tipo de solicitud.';
    } finally {
        loading.value = false;
    }
};

const editSistema = (item) => {
    sistemaForm.id = item.id;
    sistemaForm.nombre = item.nombre;
    sistemaForm.activo = Boolean(item.activo);
};

const editPrioridad = (item) => {
    prioridadForm.id = item.id;
    prioridadForm.nombre = item.nombre;
    prioridadForm.orden = item.orden;
    prioridadForm.activo = Boolean(item.activo);
};

const editTipo = (item) => {
    tipoForm.id = item.id;
    tipoForm.nombre = item.nombre;
    tipoForm.activo = Boolean(item.activo);
};

const toggleSistema = async (item) => {
    await window.axios.patch(`/api/admin/catalogos/sistemas/${item.id}`, { activo: !item.activo });
    await fetchAll();
};

const togglePrioridad = async (item) => {
    await window.axios.patch(`/api/admin/catalogos/prioridades/${item.id}`, { activo: !item.activo });
    await fetchAll();
};

const toggleTipo = async (item) => {
    await window.axios.patch(`/api/admin/catalogos/tipos-solicitud/${item.id}`, { activo: !item.activo });
    await fetchAll();
};

const activeLabel = (value) => (value ? 'Activo' : 'Inactivo');
const badgeClass = (value) => (value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600');

const pageTitle = computed(() => 'Administracion - Catalogos');

onMounted(() => {
    fetchAll();
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="pageTitle" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catalogos</h2>
                <Link class="text-sm text-indigo-600 hover:text-indigo-700" :href="route('admin.users')">Usuarios</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <section class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Gestion</h3>
                        <SecondaryButton :disabled="loading" @click="fetchAll">Refrescar</SecondaryButton>
                    </div>
                    <InputError v-if="error" class="mt-4" :message="error" />
                </section>

                <section class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Sistemas</h3>
                        <form class="mt-3 grid gap-4 md:grid-cols-3 items-end" @submit.prevent="saveSistema">
                            <div class="md:col-span-2">
                                <InputLabel for="sistema_nombre" value="Nombre" />
                                <TextInput id="sistema_nombre" v-model="sistemaForm.nombre" type="text" class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="formErrors.nombre?.[0]" />
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input v-model="sistemaForm.activo" type="checkbox" class="rounded border-gray-300" />
                                    Activo
                                </label>
                                <PrimaryButton :disabled="loading">{{ sistemaForm.id ? 'Guardar' : 'Crear' }}</PrimaryButton>
                            </div>
                        </form>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Nombre</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Estado</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-700">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="item in sistemas" :key="item.id">
                                        <td class="px-3 py-2">{{ item.nombre }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" :class="badgeClass(item.activo)">
                                                {{ activeLabel(item.activo) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-right space-x-2">
                                            <SecondaryButton @click="editSistema(item)">Editar</SecondaryButton>
                                            <SecondaryButton @click="toggleSistema(item)">Toggle</SecondaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900">Prioridades</h3>
                        <form class="mt-3 grid gap-4 md:grid-cols-4 items-end" @submit.prevent="savePrioridad">
                            <div class="md:col-span-2">
                                <InputLabel for="prioridad_nombre" value="Nombre" />
                                <TextInput id="prioridad_nombre" v-model="prioridadForm.nombre" type="text" class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="formErrors.nombre?.[0]" />
                            </div>
                            <div>
                                <InputLabel for="prioridad_orden" value="Orden" />
                                <TextInput id="prioridad_orden" v-model="prioridadForm.orden" type="number" class="mt-1 block w-full" min="0" />
                                <InputError class="mt-2" :message="formErrors.orden?.[0]" />
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input v-model="prioridadForm.activo" type="checkbox" class="rounded border-gray-300" />
                                    Activo
                                </label>
                                <PrimaryButton :disabled="loading">{{ prioridadForm.id ? 'Guardar' : 'Crear' }}</PrimaryButton>
                            </div>
                        </form>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Nombre</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Orden</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Estado</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-700">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="item in prioridades" :key="item.id">
                                        <td class="px-3 py-2">{{ item.nombre }}</td>
                                        <td class="px-3 py-2">{{ item.orden }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" :class="badgeClass(item.activo)">
                                                {{ activeLabel(item.activo) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-right space-x-2">
                                            <SecondaryButton @click="editPrioridad(item)">Editar</SecondaryButton>
                                            <SecondaryButton @click="togglePrioridad(item)">Toggle</SecondaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900">Tipos de solicitud</h3>
                        <form class="mt-3 grid gap-4 md:grid-cols-3 items-end" @submit.prevent="saveTipo">
                            <div class="md:col-span-2">
                                <InputLabel for="tipo_nombre" value="Nombre" />
                                <TextInput id="tipo_nombre" v-model="tipoForm.nombre" type="text" class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="formErrors.nombre?.[0]" />
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input v-model="tipoForm.activo" type="checkbox" class="rounded border-gray-300" />
                                    Activo
                                </label>
                                <PrimaryButton :disabled="loading">{{ tipoForm.id ? 'Guardar' : 'Crear' }}</PrimaryButton>
                            </div>
                        </form>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Nombre</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Estado</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-700">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="item in tiposSolicitud" :key="item.id">
                                        <td class="px-3 py-2">{{ item.nombre }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" :class="badgeClass(item.activo)">
                                                {{ activeLabel(item.activo) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-right space-x-2">
                                            <SecondaryButton @click="editTipo(item)">Editar</SecondaryButton>
                                            <SecondaryButton @click="toggleTipo(item)">Toggle</SecondaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

