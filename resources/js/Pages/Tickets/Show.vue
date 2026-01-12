<script setup>
import { onBeforeUnmount, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
    pollInterval: {
        type: Number,
        default: 60000,
    },
});

const formatDate = (value) => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleString();
};

let intervalId;

const reloadTicket = () => {
    router.reload({
        only: ['ticket'],
        preserveScroll: true,
    });
};

onMounted(() => {
    if (props.pollInterval > 0) {
        intervalId = window.setInterval(reloadTicket, props.pollInterval);
    }
});

onBeforeUnmount(() => {
    if (intervalId) {
        window.clearInterval(intervalId);
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Ticket #${ticket.id}`" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('tickets.index')"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-full border border-gray-200 text-gray-600 hover:text-gray-900 hover:border-gray-300"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M12.707 4.293a1 1 0 010 1.414L9.414 9H16a1 1 0 110 2H9.414l3.293 3.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="sr-only">Volver al listado</span>
                </Link>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Ticket #{{ ticket.id }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Actualizacion automatica cada 60s
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                    <div>
                        <div class="text-sm text-gray-500">Asunto</div>
                        <div class="text-lg font-semibold text-gray-900">{{ ticket.asunto }}</div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-sm text-gray-500">Estado</div>
                            <div class="text-base text-gray-900">{{ ticket.estado || 'Sin estado' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Sistema</div>
                            <div class="text-base text-gray-900">{{ ticket.sistema || 'Sin sistema' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Descripcion</div>
                        <div class="mt-2 whitespace-pre-line text-gray-900">{{ ticket.descripcion }}</div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 text-sm text-gray-500">
                        <div>Creado: {{ formatDate(ticket.created_at) }}</div>
                        <div>Actualizado: {{ formatDate(ticket.updated_at) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
