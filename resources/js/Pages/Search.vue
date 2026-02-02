<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    catalogs: {
        type: Object,
        default: () => ({
            estados: [],
            sistemas: [],
        }),
    },
});

const filters = reactive({
    asunto: '',
    estado_id: '',
    sistema_id: '',
});

const tickets = ref([]);
const meta = ref(null);
const page = ref(1);
const loading = ref(false);
const error = ref('');

const hasMore = computed(() => {
    const currentMeta = meta.value;
    return currentMeta && currentMeta.current_page < currentMeta.last_page;
});

const normalizeId = (value) => {
    if (value === '' || value === null || value === undefined) {
        return null;
    }

    return Number(value);
};

const fetchTickets = async ({ page: requestedPage = 1, append = false } = {}) => {
    loading.value = true;
    error.value = '';

    try {
        const response = await window.axios.get('/api/tickets/busqueda', {
            params: {
                asunto: filters.asunto || undefined,
                estado_id: normalizeId(filters.estado_id) ?? undefined,
                sistema_id: normalizeId(filters.sistema_id) ?? undefined,
                per_page: 20,
                page: requestedPage,
            },
        });

        const items = response?.data?.data ?? [];
        tickets.value = append ? [...tickets.value, ...items] : items;
        meta.value = response?.data?.meta ?? null;
        page.value = meta.value?.current_page ?? requestedPage;
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudieron cargar los resultados.';
    } finally {
        loading.value = false;
    }
};

const search = async () => {
    await fetchTickets({ page: 1, append: false });
};

const loadMore = async () => {
    if (loading.value || !hasMore.value) {
        return;
    }

    await fetchTickets({ page: page.value + 1, append: true });
};

const resetFilters = () => {
    filters.asunto = '';
    filters.estado_id = '';
    filters.sistema_id = '';
    tickets.value = [];
    meta.value = null;
    page.value = 1;
    error.value = '';
};

onMounted(() => {
    fetchTickets();
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Busqueda" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Busqueda</h2>
                <span class="text-sm text-gray-500">Filtra tickets por asunto, estado y aplicacion</span>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <section class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>

                    <form class="mt-4 grid gap-4 md:grid-cols-4 items-end" @submit.prevent="search">
                        <div class="md:col-span-2">
                            <InputLabel for="asunto" value="Asunto" />
                            <TextInput id="asunto" v-model="filters.asunto" type="text" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <InputLabel for="estado" value="Estado" />
                            <select
                                id="estado"
                                v-model="filters.estado_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                            >
                                <option value="">Todos</option>
                                <option v-for="estado in catalogs.estados" :key="estado.id" :value="estado.id">
                                    {{ estado.nombre }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="sistema" value="Aplicacion" />
                            <select
                                id="sistema"
                                v-model="filters.sistema_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                            >
                                <option value="">Todas</option>
                                <option v-for="sistema in catalogs.sistemas" :key="sistema.id" :value="sistema.id">
                                    {{ sistema.nombre }}
                                    <span v-if="sistema.activo === false"> (inactivo)</span>
                                </option>
                            </select>
                        </div>

                        <div class="flex gap-2 md:col-span-4">
                            <PrimaryButton :disabled="loading">Buscar</PrimaryButton>
                            <SecondaryButton type="button" :disabled="loading" @click="resetFilters">Limpiar</SecondaryButton>
                        </div>
                    </form>

                    <InputError v-if="error" class="mt-4" :message="error" />
                </section>

                <section class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">Resultados</h3>
                        <span v-if="meta" class="text-sm text-gray-500">
                            {{ meta.total }} tickets
                        </span>
                    </div>

                    <div v-if="loading && tickets.length === 0" class="mt-4 text-sm text-gray-500">Buscando...</div>
                    <div v-else-if="tickets.length === 0" class="mt-4 text-sm text-gray-500">Sin resultados.</div>

                    <ul v-else class="mt-4 divide-y divide-gray-200">
                        <li v-for="ticket in tickets" :key="ticket.id" class="py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <Link
                                        :href="route('tickets.show', ticket.id)"
                                        class="text-base font-semibold text-indigo-600 hover:text-indigo-800"
                                    >
                                        #{{ ticket.id }} · {{ ticket.asunto }}
                                    </Link>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <span v-if="ticket.estado">Estado: {{ ticket.estado }}</span>
                                        <span v-if="ticket.sistema"> · Aplicacion: {{ ticket.sistema }}</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ ticket.updated_at }}
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div v-if="hasMore" class="mt-4">
                        <SecondaryButton :disabled="loading" @click="loadMore">Cargar mas</SecondaryButton>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

