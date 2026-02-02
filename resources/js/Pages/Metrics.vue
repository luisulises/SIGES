<script setup>
import { onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const loading = ref(false);
const error = ref('');
const metrics = ref(null);

const fetchMetrics = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await window.axios.get('/api/tickets/metricas');
        metrics.value = response?.data?.data ?? null;
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudieron cargar las metricas.';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchMetrics();
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Metricas" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Metricas</h2>
                <PrimaryButton :disabled="loading" @click="fetchMetrics">Refrescar</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <section class="bg-white shadow sm:rounded-lg p-6">
                    <div v-if="loading && !metrics" class="text-sm text-gray-500">Cargando...</div>
                    <InputError v-else-if="error" :message="error" />

                    <template v-else-if="metrics">
                        <div class="text-sm text-gray-500">Total tickets visibles</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ metrics.total }}</div>

                        <div class="mt-6 grid gap-6 lg:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 p-4">
                                <h3 class="text-base font-semibold text-gray-900">Por estado</h3>
                                <div v-if="metrics.por_estado.length === 0" class="mt-3 text-sm text-gray-500">Sin datos.</div>
                                <ul v-else class="mt-3 space-y-2">
                                    <li
                                        v-for="row in metrics.por_estado"
                                        :key="row.estado_id"
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <span class="text-gray-700">{{ row.estado }}</span>
                                        <span class="font-medium text-gray-900">{{ row.total }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4">
                                <h3 class="text-base font-semibold text-gray-900">Por prioridad</h3>
                                <div v-if="metrics.por_prioridad.length === 0" class="mt-3 text-sm text-gray-500">Sin datos.</div>
                                <ul v-else class="mt-3 space-y-2">
                                    <li
                                        v-for="row in metrics.por_prioridad"
                                        :key="String(row.prioridad_id) + row.prioridad"
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <span class="text-gray-700">{{ row.prioridad }}</span>
                                        <span class="font-medium text-gray-900">{{ row.total }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </template>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

