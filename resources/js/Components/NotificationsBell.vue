<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import { Link } from '@inertiajs/vue3';

const pollInterval = 60000;

const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
const error = ref('');
const page = ref(1);
const meta = ref(null);
let timerId;

const extractCollection = (response) => response?.data?.data ?? [];
const hasMore = computed(() => {
    const currentMeta = meta.value;
    return currentMeta && currentMeta.current_page < currentMeta.last_page;
});

const fetchNotifications = async ({ page: requestedPage = 1, append = false } = {}) => {
    loading.value = true;
    error.value = '';

    try {
        const response = await window.axios.get('/api/notificaciones', {
            params: {
                page: requestedPage,
            },
        });

        const items = extractCollection(response);
        notifications.value = append ? [...notifications.value, ...items] : items;
        unreadCount.value = response?.data?.meta?.unread_count ?? 0;
        meta.value = response?.data?.meta ?? null;
        page.value = meta.value?.current_page ?? requestedPage;
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudieron cargar las notificaciones.';
    } finally {
        loading.value = false;
    }
};

const pollNotifications = () => {
    if (document.visibilityState && document.visibilityState !== 'visible') {
        return;
    }

    fetchNotifications();
};

const loadMore = async () => {
    if (loading.value || !hasMore.value) {
        return;
    }

    await fetchNotifications({ page: page.value + 1, append: true });
};

const markAsRead = async (id) => {
    try {
        await window.axios.post(`/api/notificaciones/${id}/leer`);
        await fetchNotifications();
    } catch (e) {
        const data = e?.response?.data;
        error.value = data?.message || 'No se pudo marcar como leída.';
    }
};

const hasNotifications = computed(() => notifications.value.length > 0);

onMounted(() => {
    fetchNotifications();
    timerId = window.setInterval(pollNotifications, pollInterval);
});

onBeforeUnmount(() => {
    if (timerId) {
        window.clearInterval(timerId);
        timerId = undefined;
    }
});
</script>

<template>
    <div class="ms-3 relative">
        <Dropdown align="right" width="96">
            <template #trigger>
                <button
                    type="button"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50 focus:outline-none transition"
                    @click="
                        () => {
                            if (notifications.length === 0) fetchNotifications();
                        }
                    "
                >
                    <svg
                        class="h-6 w-6"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 0 0-5-6.71V3a2 2 0 0 0-4 0v1.29A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2Z"
                        />
                    </svg>

                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full text-xs font-semibold bg-red-600 text-white"
                    >
                        {{ unreadCount > 99 ? '99+' : unreadCount }}
                    </span>
                </button>
            </template>

            <template #content>
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-gray-800">Notificaciones</div>
                    <button
                        type="button"
                        class="text-xs text-indigo-600 hover:text-indigo-700"
                        :disabled="loading"
                        @click="fetchNotifications"
                    >
                        Refrescar
                    </button>
                </div>

                <div class="max-h-96 overflow-auto">
                    <div v-if="loading" class="px-4 py-3 text-sm text-gray-500">Cargando...</div>
                    <div v-else-if="error" class="px-4 py-3 text-sm text-red-600">{{ error }}</div>
                    <div v-else-if="!hasNotifications" class="px-4 py-3 text-sm text-gray-500">Sin notificaciones.</div>

                    <ul v-else class="divide-y divide-gray-100">
                        <li v-for="n in notifications" :key="n.id" class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ n.tipo_evento }}</span>
                                        <span v-if="n.ticket?.id" class="text-gray-600">
                                            · Ticket #{{ n.ticket.id }}
                                        </span>
                                    </div>
                                    <div v-if="n.ticket?.asunto" class="text-xs text-gray-500 truncate">
                                        {{ n.ticket.asunto }}
                                    </div>
                                    <div v-if="n.created_at" class="text-xs text-gray-400">
                                        {{ n.created_at }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <Link
                                        v-if="n.ticket?.id"
                                        class="text-xs text-indigo-600 hover:text-indigo-700"
                                        :href="route('tickets.show', n.ticket.id)"
                                    >
                                        Abrir
                                    </Link>

                                    <button
                                        v-if="!n.leido_at"
                                        type="button"
                                        class="text-xs text-gray-600 hover:text-gray-800"
                                        @click="markAsRead(n.id)"
                                    >
                                        Marcar leída
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div v-if="hasMore" class="px-4 py-3 border-t border-gray-100">
                        <button
                            type="button"
                            class="text-xs text-indigo-600 hover:text-indigo-700"
                            :disabled="loading"
                            @click="loadMore"
                        >
                            Cargar más
                        </button>
                    </div>
                </div>
            </template>
        </Dropdown>
    </div>
</template>
