<script setup>
import { onBeforeUnmount, onMounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tickets: {
        type: Object,
        default: () => ({
            data: [],
            meta: null,
            links: [],
        }),
    },
    sistemas: {
        type: Array,
        default: () => [],
    },
    pollInterval: {
        type: Number,
        default: 60000,
    },
});

const ticketItems = () => props.tickets?.data ?? [];
const totalTickets = () => props.tickets?.meta?.total ?? ticketItems().length;

const form = useForm({
    asunto: '',
    sistema_id: '',
    referencia_ticket_id: '',
    descripcion: '',
});

const submit = () => {
    form.post(route('tickets.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('asunto', 'sistema_id', 'referencia_ticket_id', 'descripcion');
        },
    });
};

const formatDate = (value) => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleString();
};

let intervalId;

const reloadTickets = () => {
    if (document.visibilityState && document.visibilityState !== 'visible') {
        return;
    }

    if (form.processing) {
        return;
    }

    router.reload({
        only: ['tickets'],
        preserveScroll: true,
        preserveState: true,
    });
};

onMounted(() => {
    if (props.pollInterval > 0) {
        intervalId = window.setInterval(reloadTickets, props.pollInterval);
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
        <Head title="Tickets" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tickets</h2>
                <span class="text-sm text-gray-500">Actualizacion automatica cada 60s</span>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid gap-6 lg:grid-cols-[360px,1fr]">
                    <section class="bg-white shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Nuevo ticket</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Completa la solicitud con asunto, aplicacion y descripcion.
                        </p>

                        <form class="mt-6 space-y-4" @submit.prevent="submit">
                            <div>
                                <InputLabel for="asunto" value="Asunto" />
                                <TextInput
                                    id="asunto"
                                    v-model="form.asunto"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.asunto" />
                            </div>

                            <div>
                                <InputLabel for="sistema" value="Aplicacion" />
                                <select
                                    id="sistema"
                                    v-model="form.sistema_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    required
                                >
                                    <option value="" disabled>Selecciona una aplicacion</option>
                                    <option v-for="sistema in sistemas" :key="sistema.id" :value="sistema.id">
                                        {{ sistema.nombre }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.sistema_id" />
                            </div>

                            <div>
                                <InputLabel for="descripcion" value="Descripcion" />
                                <textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    rows="4"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.descripcion" />
                            </div>

                            <div>
                                <InputLabel for="referencia_ticket_id" value="Referenciar ticket cerrado/cancelado (opcional)" />
                                <TextInput
                                    id="referencia_ticket_id"
                                    v-model="form.referencia_ticket_id"
                                    type="number"
                                    class="mt-1 block w-full"
                                    min="1"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Si el problema reaparece, crea este ticket vinculado a uno Cerrado/Cancelado.
                                </p>
                                <InputError class="mt-2" :message="form.errors.referencia_ticket_id" />
                            </div>

                            <PrimaryButton :disabled="form.processing">
                                Crear ticket
                            </PrimaryButton>
                        </form>
                    </section>

                    <section class="bg-white shadow sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Listado</h3>
                            <span class="text-sm text-gray-500">{{ totalTickets() }} tickets</span>
                        </div>

                        <div v-if="ticketItems().length === 0" class="mt-6 text-sm text-gray-500">
                            No hay tickets disponibles.
                        </div>

                        <div v-else class="mt-6 divide-y divide-gray-200">
                            <div v-for="ticket in ticketItems()" :key="ticket.id" class="py-4 flex flex-col gap-2">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <Link
                                            :href="route('tickets.show', ticket.id)"
                                            class="text-base font-semibold text-indigo-600 hover:text-indigo-800"
                                        >
                                            {{ ticket.asunto }}
                                        </Link>
                                        <div class="text-sm text-gray-500 mt-1">
                                            Aplicacion: {{ ticket.sistema || 'Sin aplicacion' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                            {{ ticket.estado || 'Sin estado' }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-2">
                                            {{ formatDate(ticket.updated_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="tickets.links?.length" class="mt-6 flex flex-wrap gap-2 justify-center">
                            <Link
                                v-for="(link, index) in tickets.links"
                                :key="index"
                                :href="link.url || ''"
                                class="px-3 py-1 rounded-md text-sm border border-gray-200"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active,
                                    'text-gray-500 pointer-events-none opacity-50': !link.url,
                                    'text-gray-700 hover:bg-gray-50': link.url && !link.active,
                                }"
                                v-html="link.label"
                            />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
