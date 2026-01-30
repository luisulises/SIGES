<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
    catalogs: {
        type: Object,
        default: () => ({
            estados: [],
            prioridades: [],
            tipos_solicitud: [],
            sistemas: [],
            responsables: [],
            usuarios: [],
        }),
    },
    transiciones: {
        type: Array,
        default: () => [],
    },
    permissions: {
        type: Object,
        default: () => ({}),
    },
    pollInterval: {
        type: Number,
        default: 60000,
    },
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user ?? null);

const ticketState = reactive({ ...props.ticket });

const toDateInput = (value) => {
    if (!value) {
        return '';
    }

    const [datePart] = value.split('T');
    return datePart ?? '';
};

const syncOperativoForm = () => {
    operativoForm.responsable_id = ticketState.responsable_actual_id ?? '';
    operativoForm.prioridad_id = ticketState.prioridad_id ?? '';
    operativoForm.tipo_solicitud_id = ticketState.tipo_solicitud_id ?? '';
    operativoForm.sistema_id = ticketState.sistema_id ?? '';
    operativoForm.fecha_compromiso = toDateInput(ticketState.fecha_compromiso);
    operativoForm.fecha_entrega = toDateInput(ticketState.fecha_entrega);
    operativoForm.resolucion = ticketState.resolucion ?? '';
};

const operativoForm = reactive({
    responsable_id: ticketState.responsable_actual_id ?? '',
    prioridad_id: ticketState.prioridad_id ?? '',
    tipo_solicitud_id: ticketState.tipo_solicitud_id ?? '',
    sistema_id: ticketState.sistema_id ?? '',
    fecha_compromiso: toDateInput(ticketState.fecha_compromiso),
    fecha_entrega: toDateInput(ticketState.fecha_entrega),
    resolucion: ticketState.resolucion ?? '',
});

const estadoForm = reactive({
    estado: '',
});

const estadoError = ref('');
const actionError = ref('');
const actionSuccess = ref('');
const operativoErrors = ref({});
const processing = reactive({
    estado: false,
    operativo: false,
    cerrar: false,
    cancelar: false,
});

const roleName = computed(() => props.permissions?.role ?? '');
const isCliente = computed(() => roleName.value === 'cliente_interno');
const isSoporte = computed(() => roleName.value === 'soporte');
const isCoordinador = computed(() => roleName.value === 'coordinador');
const isAdmin = computed(() => roleName.value === 'admin');
const isRolInterno = computed(() => isSoporte.value || isCoordinador.value || isAdmin.value);
const isResponsable = computed(() => authUser.value && ticketState.responsable_actual_id === authUser.value.id);
const isSolicitante = computed(() => authUser.value && ticketState.solicitante_id === authUser.value.id);

const canOperate = computed(() => props.permissions?.can_operate ?? false);
const canCloseCancel = computed(() => props.permissions?.can_close_cancel ?? false);
const canAssign = computed(() => isAdmin.value || (isCoordinador.value && props.permissions?.is_coordinador_sistema));
const canCoordinatorFields = computed(() => isAdmin.value || (isCoordinador.value && props.permissions?.is_coordinador_sistema));
const canSoporteFields = computed(() => isAdmin.value || (isSoporte.value && isResponsable.value));
const canEditOperativo = computed(() => canAssign.value || canCoordinatorFields.value || canSoporteFields.value);
const canCrearComentario = computed(() => (isRolInterno.value ? canOperate.value : isSolicitante.value));
const canCrearComentarioInterno = computed(() => isRolInterno.value && canOperate.value);
const canGestionarInvolucrados = computed(() => canAssign.value);

const catalogs = computed(() => props.catalogs);

let successTimeoutId;
let collaborationSuccessTimeoutId;

const setSuccess = (message) => {
    actionSuccess.value = message;

    if (successTimeoutId) {
        window.clearTimeout(successTimeoutId);
    }

    successTimeoutId = window.setTimeout(() => {
        actionSuccess.value = '';
        successTimeoutId = undefined;
    }, 4000);
};

const collaborationSuccess = ref('');

const setCollaborationSuccess = (message) => {
    collaborationSuccess.value = message;

    if (collaborationSuccessTimeoutId) {
        window.clearTimeout(collaborationSuccessTimeoutId);
    }

    collaborationSuccessTimeoutId = window.setTimeout(() => {
        collaborationSuccess.value = '';
        collaborationSuccessTimeoutId = undefined;
    }, 4000);
};

const resolveNombre = (items, id, fallback) => {
    if (!id) {
        return fallback;
    }

    return items.find((item) => item.id === id)?.nombre ?? fallback;
};

const estadoLabel = computed(() => resolveNombre(catalogs.value.estados, ticketState.estado_id, ticketState.estado || 'Sin estado'));
const sistemaLabel = computed(() => resolveNombre(catalogs.value.sistemas, ticketState.sistema_id, ticketState.sistema || 'Sin aplicacion'));
const responsableLabel = computed(() => resolveNombre(catalogs.value.responsables, ticketState.responsable_actual_id, ticketState.responsable || 'Sin responsable'));
const prioridadLabel = computed(() => resolveNombre(catalogs.value.prioridades, ticketState.prioridad_id, 'Sin prioridad'));
const tipoLabel = computed(() => resolveNombre(catalogs.value.tipos_solicitud, ticketState.tipo_solicitud_id, 'Sin tipo'));

const transicionesEstado = computed(() =>
    props.transiciones.filter((transicion) => !['Cerrado', 'Cancelado'].includes(transicion.nombre))
);

const closeAllowed = computed(() => props.transiciones.some((transicion) => transicion.nombre === 'Cerrado'));
const cancelAllowed = computed(() => props.transiciones.some((transicion) => transicion.nombre === 'Cancelado'));

const selectedTransition = computed(() =>
    props.transiciones.find((transicion) => transicion.nombre === estadoForm.estado)
);

const estadoRequiresResponsable = computed(() => selectedTransition.value?.requiere_responsable);
const estadoHint = computed(() => {
    if (!estadoRequiresResponsable.value) {
        return '';
    }

    if (ticketState.responsable_actual_id) {
        return '';
    }

    return 'Asigna un responsable antes de aplicar esta transicion.';
});

const fieldError = (errors, field) => {
    const value = errors?.[field];
    return Array.isArray(value) ? value[0] : value || '';
};

const formatDate = (value) => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleString();
};

const extractTicket = (response) => response?.data?.data ?? response?.data ?? null;
const extractCollection = (response) => response?.data?.data ?? [];

const applyTicketUpdate = (payload) => {
    if (!payload) {
        return;
    }

    Object.assign(ticketState, payload);
    syncOperativoForm();
};

const refreshTransitions = () => {
    router.reload({
        only: ['transiciones'],
        preserveScroll: true,
        preserveState: true,
    });
};

const normalizeId = (value) => {
    if (value === '' || value === null || value === undefined) {
        return null;
    }

    return Number(value);
};

const buildOperativoPayload = () => {
    const payload = {};

    if (canAssign.value) {
        const responsableId = normalizeId(operativoForm.responsable_id);
        if (responsableId !== ticketState.responsable_actual_id) {
            payload.responsable_id = responsableId;
        }
    }

    if (canCoordinatorFields.value) {
        const prioridadId = normalizeId(operativoForm.prioridad_id);
        if (prioridadId !== ticketState.prioridad_id) {
            payload.prioridad_id = prioridadId;
        }

        const sistemaId = normalizeId(operativoForm.sistema_id);
        if (sistemaId !== ticketState.sistema_id) {
            payload.sistema_id = sistemaId;
        }

        const fechaCompromiso = operativoForm.fecha_compromiso || null;
        const currentCompromiso = ticketState.fecha_compromiso ? toDateInput(ticketState.fecha_compromiso) : null;
        if (fechaCompromiso !== currentCompromiso) {
            payload.fecha_compromiso = fechaCompromiso;
        }
    }

    if (canSoporteFields.value) {
        const tipoId = normalizeId(operativoForm.tipo_solicitud_id);
        if (tipoId !== ticketState.tipo_solicitud_id) {
            payload.tipo_solicitud_id = tipoId;
        }

        const fechaEntrega = operativoForm.fecha_entrega || null;
        const currentEntrega = ticketState.fecha_entrega ? toDateInput(ticketState.fecha_entrega) : null;
        if (fechaEntrega !== currentEntrega) {
            payload.fecha_entrega = fechaEntrega;
        }

        const resolucion = operativoForm.resolucion || null;
        if (resolucion !== (ticketState.resolucion || null)) {
            payload.resolucion = resolucion;
        }
    }

    return payload;
};

const comentarios = ref([]);
const comentariosLoading = ref(false);
const comentariosError = ref('');
const mostrarInternos = ref(false);

const comentarioForm = reactive({
    cuerpo: '',
    visibilidad: 'publico',
    archivos: [],
});
const comentarioErrors = ref({});
const comentarioProcessing = ref(false);
const comentarioSubmitError = ref('');
const comentarioAdjuntoError = ref('');
const comentarioArchivosInput = ref(null);

const involucrados = ref([]);
const involucradosLoading = ref(false);
const involucradosError = ref('');
const involucradoForm = reactive({
    usuario_id: '',
});
const involucradoErrors = ref({});
const involucradoProcessing = ref(false);
const involucradoSubmitError = ref('');
const removingInvolucrado = reactive({});

const visibleComentarios = computed(() => {
    if (!isRolInterno.value || mostrarInternos.value) {
        return comentarios.value;
    }

    return comentarios.value.filter((comentario) => comentario.visibilidad === 'publico');
});

const fetchComentarios = async () => {
    comentariosError.value = '';
    comentariosLoading.value = true;

    try {
        const response = await window.axios.get(`/api/tickets/${ticketState.id}/comentarios`);
        comentarios.value = extractCollection(response);
    } catch (error) {
        const data = error.response?.data;
        comentariosError.value = data?.message || 'No se pudieron cargar los comentarios.';
    } finally {
        comentariosLoading.value = false;
    }
};

const fetchInvolucrados = async () => {
    involucradosError.value = '';
    involucradosLoading.value = true;

    try {
        const response = await window.axios.get(`/api/tickets/${ticketState.id}/involucrados`);
        involucrados.value = extractCollection(response);
    } catch (error) {
        const data = error.response?.data;
        involucradosError.value = data?.message || 'No se pudieron cargar los involucrados.';
    } finally {
        involucradosLoading.value = false;
    }
};

const refreshColaboracion = () => {
    fetchComentarios();
    fetchInvolucrados();
};

const canManageTiempo = computed(() => canOperate.value);

const historial = ref([]);
const historialLoading = ref(false);
const historialError = ref('');
const historialPerPage = 50;
const historialMeta = ref(null);
const historialPage = ref(1);
const historialHasMore = computed(() => {
    const meta = historialMeta.value;
    return meta && meta.current_page < meta.last_page;
});

const fetchHistorial = async ({ page = 1, append = false } = {}) => {
    historialError.value = '';
    historialLoading.value = true;

    try {
        const response = await window.axios.get(`/api/tickets/${ticketState.id}/historial`, {
            params: {
                page,
                per_page: historialPerPage,
            },
        });

        const items = extractCollection(response);
        historial.value = append ? [...historial.value, ...items] : items;
        historialMeta.value = response?.data?.meta ?? null;
        historialPage.value = page;
    } catch (error) {
        const data = error.response?.data;
        historialError.value = data?.message || 'No se pudo cargar el historial.';
    } finally {
        historialLoading.value = false;
    }
};

const loadMoreHistorial = async () => {
    if (historialLoading.value || !historialHasMore.value) {
        return;
    }

    await fetchHistorial({ page: historialPage.value + 1, append: true });
};

const relaciones = ref([]);
const relacionesLoading = ref(false);
const relacionesError = ref('');

const relacionForm = reactive({
    ticket_relacionado_id: '',
    tipo_relacion: 'relacionado',
});
const relacionErrors = ref({});
const relacionProcessing = ref(false);
const relacionSubmitError = ref('');

const fetchRelaciones = async () => {
    relacionesError.value = '';
    relacionesLoading.value = true;

    try {
        const response = await window.axios.get(`/api/tickets/${ticketState.id}/relaciones`);
        relaciones.value = extractCollection(response);
    } catch (error) {
        const data = error.response?.data;
        relacionesError.value = data?.message || 'No se pudieron cargar las relaciones.';
    } finally {
        relacionesLoading.value = false;
    }
};

const submitRelacion = async () => {
    relacionErrors.value = {};
    relacionSubmitError.value = '';

    if (!relacionForm.ticket_relacionado_id) {
        relacionErrors.value = { ticket_relacionado_id: ['Ingresa un ticket id.'] };
        return;
    }

    relacionProcessing.value = true;

    try {
        await window.axios.post(`/api/tickets/${ticketState.id}/relaciones`, {
            ticket_relacionado_id: Number(relacionForm.ticket_relacionado_id),
            tipo_relacion: relacionForm.tipo_relacion,
        });

        relacionForm.ticket_relacionado_id = '';
        relacionForm.tipo_relacion = 'relacionado';

        router.reload({
            only: ['ticket', 'transiciones'],
            preserveScroll: true,
            preserveState: true,
        });

        await fetchRelaciones();
        await fetchHistorial();
        setSuccess('Relacion creada.');
    } catch (error) {
        const data = error.response?.data;
        relacionErrors.value = data?.errors ?? {};
        relacionSubmitError.value = data?.message || 'No se pudo crear la relacion.';
    } finally {
        relacionProcessing.value = false;
    }
};

const tiempoRegistros = ref([]);
const tiempoLoading = ref(false);
const tiempoError = ref('');
const tiempoPerPage = 50;
const tiempoMeta = ref(null);
const tiempoPage = ref(1);
const tiempoHasMore = computed(() => {
    const meta = tiempoMeta.value;
    return meta && meta.current_page < meta.last_page;
});

const tiempoForm = reactive({
    minutos: '',
    nota: '',
});
const tiempoErrors = ref({});
const tiempoProcessing = ref(false);
const tiempoSubmitError = ref('');

const fetchTiempo = async ({ page = 1, append = false } = {}) => {
    if (!canManageTiempo.value) {
        return;
    }

    tiempoError.value = '';
    tiempoLoading.value = true;

    try {
        const response = await window.axios.get(`/api/tickets/${ticketState.id}/tiempo`, {
            params: {
                page,
                per_page: tiempoPerPage,
            },
        });

        const items = extractCollection(response);
        tiempoRegistros.value = append ? [...tiempoRegistros.value, ...items] : items;
        tiempoMeta.value = response?.data?.meta ?? null;
        tiempoPage.value = page;
    } catch (error) {
        const data = error.response?.data;
        tiempoError.value = data?.message || 'No se pudo cargar el tiempo.';
    } finally {
        tiempoLoading.value = false;
    }
};

const loadMoreTiempo = async () => {
    if (tiempoLoading.value || !tiempoHasMore.value) {
        return;
    }

    await fetchTiempo({ page: tiempoPage.value + 1, append: true });
};

const submitTiempo = async () => {
    tiempoErrors.value = {};
    tiempoSubmitError.value = '';

    if (!tiempoForm.minutos) {
        tiempoErrors.value = { minutos: ['Ingresa minutos.'] };
        return;
    }

    tiempoProcessing.value = true;

    try {
        await window.axios.post(`/api/tickets/${ticketState.id}/tiempo`, {
            minutos: Number(tiempoForm.minutos),
            nota: tiempoForm.nota || null,
        });

        tiempoForm.minutos = '';
        tiempoForm.nota = '';

        await fetchTiempo();
        await fetchHistorial();
        setSuccess('Tiempo registrado.');
    } catch (error) {
        const data = error.response?.data;
        tiempoErrors.value = data?.errors ?? {};
        tiempoSubmitError.value = data?.message || 'No se pudo registrar el tiempo.';
    } finally {
        tiempoProcessing.value = false;
    }
};

const refreshTrazabilidad = () => {
    fetchHistorial();
    fetchRelaciones();
    fetchTiempo();
};

const labelFromCatalog = (items, id) => {
    if (!items || id === null || id === undefined) {
        return id === null || id === undefined ? 'Sin' : String(id);
    }

    const found = items.find((item) => item.id === id);
    return found?.nombre || String(id);
};

const usuarioLabel = (id) => {
    if (id === null || id === undefined) {
        return 'Sin';
    }

    const usuario = (catalogs.value.usuarios || []).find((item) => item.id === id);
    if (usuario?.nombre) {
        return usuario.nombre;
    }

    const responsable = (catalogs.value.responsables || []).find((item) => item.id === id);
    if (responsable?.nombre) {
        return responsable.nombre;
    }

    return String(id);
};

const relationOtherTicket = (relacion) => {
    return relacion.ticket_id === ticketState.id ? relacion.ticket_relacionado : relacion.ticket;
};

const auditSummary = (evento) => {
    const antes = evento?.valor_antes || {};
    const despues = evento?.valor_despues || {};

    switch (evento?.tipo_evento) {
        case 'estado_cambiado':
            return `Estado: ${labelFromCatalog(catalogs.value.estados, antes.estado_id)} → ${labelFromCatalog(
                catalogs.value.estados,
                despues.estado_id
            )}`;
        case 'asignacion_cambiada':
            return `Responsable: ${usuarioLabel(antes.responsable_actual_id)} → ${usuarioLabel(despues.responsable_actual_id)}`;
        case 'prioridad_cambiada':
            return `Prioridad: ${labelFromCatalog(catalogs.value.prioridades, antes.prioridad_id)} → ${labelFromCatalog(
                catalogs.value.prioridades,
                despues.prioridad_id
            )}`;
        case 'tipo_cambiado':
            return `Tipo: ${labelFromCatalog(catalogs.value.tipos_solicitud, antes.tipo_solicitud_id)} → ${labelFromCatalog(
                catalogs.value.tipos_solicitud,
                despues.tipo_solicitud_id
            )}`;
        case 'sistema_cambiado':
            return `Aplicacion: ${labelFromCatalog(catalogs.value.sistemas, antes.sistema_id)} → ${labelFromCatalog(
                catalogs.value.sistemas,
                despues.sistema_id
            )}`;
        case 'fecha_compromiso_cambiada':
            return `Compromiso: ${antes.fecha_compromiso || 'Sin'} → ${despues.fecha_compromiso || 'Sin'}`;
        case 'fecha_entrega_cambiada':
            return `Entrega: ${antes.fecha_entrega || 'Sin'} → ${despues.fecha_entrega || 'Sin'}`;
        case 'resolucion_registrada':
            return 'Resolucion actualizada.';
        case 'cierre':
            return 'Ticket cerrado.';
        case 'cancelacion':
            return 'Ticket cancelado.';
        case 'relacion_creada':
            return `Relacion creada: ${despues.tipo_relacion} (#${despues.ticket_relacionado_id}).`;
        case 'tiempo_registrado':
            return `Tiempo registrado: ${despues.minutos} min.`;
        default:
            return evento?.tipo_evento || 'Evento';
    }
};

const onComentarioArchivosChange = (event) => {
    comentarioAdjuntoError.value = '';
    const files = event.target?.files ? Array.from(event.target.files) : [];
    comentarioForm.archivos = files;
};

const uploadAdjunto = async (file, comentarioId = null) => {
    const formData = new FormData();
    formData.append('archivo', file);
    if (comentarioId) {
        formData.append('comentario_id', String(comentarioId));
    }

    const response = await window.axios.post(`/api/tickets/${ticketState.id}/adjuntos`, formData);
    return response?.data?.data ?? null;
};

const submitComentario = async () => {
    comentarioErrors.value = {};
    comentarioSubmitError.value = '';
    comentarioAdjuntoError.value = '';

    if (!comentarioForm.cuerpo) {
        comentarioErrors.value = { cuerpo: ['Escribe un comentario.'] };
        return;
    }

    comentarioProcessing.value = true;

    try {
        const response = await window.axios.post(`/api/tickets/${ticketState.id}/comentarios`, {
            cuerpo: comentarioForm.cuerpo,
            visibilidad: comentarioForm.visibilidad,
        });

        const comentario = response?.data?.data;

        if (comentario && comentarioForm.archivos.length > 0) {
            for (const file of comentarioForm.archivos) {
                try {
                    await uploadAdjunto(file, comentario.id);
                } catch (error) {
                    const data = error.response?.data;
                    comentarioAdjuntoError.value =
                        fieldError(data?.errors, 'archivo') || data?.message || 'No se pudo subir el adjunto.';
                    break;
                }
            }
        }

        comentarioForm.cuerpo = '';
        comentarioForm.visibilidad = 'publico';
        comentarioForm.archivos = [];
        if (comentarioArchivosInput.value) {
            comentarioArchivosInput.value.value = '';
        }

        await fetchComentarios();
        setCollaborationSuccess('Comentario agregado.');
    } catch (error) {
        const data = error.response?.data;
        comentarioErrors.value = data?.errors ?? {};
        comentarioSubmitError.value = data?.message || 'No se pudo agregar el comentario.';
    } finally {
        comentarioProcessing.value = false;
    }
};

const addInvolucrado = async () => {
    involucradoErrors.value = {};
    involucradoSubmitError.value = '';

    if (!involucradoForm.usuario_id) {
        involucradoErrors.value = { usuario_id: ['Selecciona un usuario.'] };
        return;
    }

    involucradoProcessing.value = true;

    try {
        await window.axios.post(`/api/tickets/${ticketState.id}/involucrados`, {
            usuario_id: Number(involucradoForm.usuario_id),
        });

        involucradoForm.usuario_id = '';
        await fetchInvolucrados();
        setCollaborationSuccess('Involucrado agregado.');
    } catch (error) {
        const data = error.response?.data;
        involucradoErrors.value = data?.errors ?? {};
        involucradoSubmitError.value = data?.message || 'No se pudo agregar el involucrado.';
    } finally {
        involucradoProcessing.value = false;
    }
};

const removeInvolucrado = async (usuarioId) => {
    involucradosError.value = '';
    involucradoSubmitError.value = '';

    if (!window.confirm('Seguro que deseas remover este involucrado?')) {
        return;
    }

    removingInvolucrado[usuarioId] = true;

    try {
        await window.axios.delete(`/api/tickets/${ticketState.id}/involucrados/${usuarioId}`);
        await fetchInvolucrados();
        setCollaborationSuccess('Involucrado removido.');
    } catch (error) {
        const data = error.response?.data;
        involucradosError.value = data?.message || 'No se pudo remover el involucrado.';
    } finally {
        removingInvolucrado[usuarioId] = false;
    }
};

const updateOperativo = async () => {
    operativoErrors.value = {};
    actionError.value = '';
    actionSuccess.value = '';

    const payload = buildOperativoPayload();

    if (Object.keys(payload).length === 0) {
        operativoErrors.value = { operacion: 'No hay cambios para aplicar.' };
        return;
    }

    processing.operativo = true;

    try {
        const response = await window.axios.patch(`/api/tickets/${ticketState.id}/operativo`, payload);
        applyTicketUpdate(extractTicket(response));
        refreshTransitions();
        fetchHistorial();
        setSuccess('Cambios guardados.');
    } catch (error) {
        const data = error.response?.data;
        operativoErrors.value = data?.errors ?? { operacion: data?.message || 'No se pudo guardar.' };
    } finally {
        processing.operativo = false;
    }
};

const updateEstado = async () => {
    estadoError.value = '';
    actionError.value = '';
    actionSuccess.value = '';

    if (!estadoForm.estado) {
        estadoError.value = 'Selecciona un estado.';
        return;
    }

    if (estadoRequiresResponsable.value && !ticketState.responsable_actual_id) {
        estadoError.value = 'Se requiere responsable para esta transicion.';
        return;
    }

    processing.estado = true;

    try {
        const response = await window.axios.post(`/api/tickets/${ticketState.id}/estado`, {
            estado: estadoForm.estado,
        });
        applyTicketUpdate(extractTicket(response));
        estadoForm.estado = '';
        refreshTransitions();
        fetchHistorial();
        setSuccess('Estado actualizado.');
    } catch (error) {
        const data = error.response?.data;
        estadoError.value = fieldError(data?.errors, 'estado') || data?.message || 'No se pudo actualizar el estado.';
    } finally {
        processing.estado = false;
    }
};

const closeTicket = async () => {
    actionError.value = '';
    actionSuccess.value = '';

    if (!ticketState.resolucion) {
        actionError.value = 'Agrega una resolucion antes de cerrar el ticket.';
        return;
    }

    processing.cerrar = true;

    try {
        const response = await window.axios.post(`/api/tickets/${ticketState.id}/cerrar`);
        applyTicketUpdate(extractTicket(response));
        refreshTransitions();
        setSuccess('Ticket cerrado.');
        router.visit(route('tickets.index'));
    } catch (error) {
        const data = error.response?.data;
        actionError.value = data?.message || fieldError(data?.errors, 'estado') || 'No se pudo cerrar el ticket.';
    } finally {
        processing.cerrar = false;
    }
};

const cancelTicket = async () => {
    actionError.value = '';
    actionSuccess.value = '';

    if (!window.confirm('Seguro que deseas cancelar este ticket?')) {
        return;
    }

    processing.cancelar = true;

    try {
        const response = await window.axios.post(`/api/tickets/${ticketState.id}/cancelar`);
        applyTicketUpdate(extractTicket(response));
        refreshTransitions();
        setSuccess('Ticket cancelado.');
        router.visit(route('tickets.index'));
    } catch (error) {
        const data = error.response?.data;
        actionError.value = data?.message || fieldError(data?.errors, 'estado') || 'No se pudo cancelar el ticket.';
    } finally {
        processing.cancelar = false;
    }
};

let intervalId;

const reloadTicket = () => {
    const isBusy =
        processing.estado ||
        processing.operativo ||
        processing.cerrar ||
        processing.cancelar ||
        comentarioProcessing.value ||
        relacionProcessing.value ||
        tiempoProcessing.value ||
        involucradoProcessing.value ||
        comentariosLoading.value ||
        involucradosLoading.value ||
        historialLoading.value ||
        relacionesLoading.value ||
        tiempoLoading.value;

    if (isBusy) {
        return;
    }

    router.reload({
        only: ['ticket', 'transiciones'],
        preserveScroll: true,
        preserveState: true,
    });

    refreshColaboracion();
    refreshTrazabilidad();
};

onMounted(() => {
    refreshColaboracion();
    refreshTrazabilidad();

    if (props.pollInterval > 0) {
        intervalId = window.setInterval(reloadTicket, props.pollInterval);
    }
});

onBeforeUnmount(() => {
    if (intervalId) {
        window.clearInterval(intervalId);
    }

    if (successTimeoutId) {
        window.clearTimeout(successTimeoutId);
    }

    if (collaborationSuccessTimeoutId) {
        window.clearTimeout(collaborationSuccessTimeoutId);
    }
});

watch(
    () => props.ticket,
    (newTicket) => {
        Object.assign(ticketState, newTicket);
        syncOperativoForm();
    }
);
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Ticket #${ticketState.id}`" />

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
                        Ticket #{{ ticketState.id }}
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
                        <div class="text-lg font-semibold text-gray-900">{{ ticketState.asunto }}</div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-sm text-gray-500">Estado</div>
                            <div class="text-base text-gray-900">{{ estadoLabel }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Aplicacion</div>
                            <div class="text-base text-gray-900">{{ sistemaLabel }}</div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <div class="text-sm text-gray-500">Responsable</div>
                            <div class="text-base text-gray-900">{{ responsableLabel }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Prioridad</div>
                            <div class="text-base text-gray-900">{{ prioridadLabel }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Tipo</div>
                            <div class="text-base text-gray-900">{{ tipoLabel }}</div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3 text-sm text-gray-500">
                        <div>Compromiso: {{ ticketState.fecha_compromiso ? formatDate(ticketState.fecha_compromiso) : 'Sin fecha' }}</div>
                        <div>Entrega: {{ ticketState.fecha_entrega ? formatDate(ticketState.fecha_entrega) : 'Sin fecha' }}</div>
                        <div>Resolucion: {{ ticketState.resolucion ? 'Registrada' : 'Pendiente' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Descripcion</div>
                        <div class="mt-2 whitespace-pre-line text-gray-900">{{ ticketState.descripcion }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Resolucion</div>
                        <div class="mt-2 whitespace-pre-line text-gray-900">
                            {{ ticketState.resolucion || 'Sin resolucion registrada.' }}
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 text-sm text-gray-500">
                        <div>Creado: {{ formatDate(ticketState.created_at) }}</div>
                        <div>Actualizado: {{ formatDate(ticketState.updated_at) }}</div>
                        <div v-if="ticketState.cerrado_at">Cerrado: {{ formatDate(ticketState.cerrado_at) }}</div>
                        <div v-if="ticketState.cancelado_at">Cancelado: {{ formatDate(ticketState.cancelado_at) }}</div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mt-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Gestion operativa</h3>
                        <span class="text-sm text-gray-500">Actualiza sin recargar</span>
                    </div>

                    <div v-if="isCliente" class="mt-6 space-y-4">
                        <div v-if="canCloseCancel" class="space-y-4">
                            <p class="text-sm text-gray-500">
                                Puedes cerrar o cancelar este ticket cuando aplique.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <SecondaryButton :disabled="!closeAllowed || processing.cerrar" @click="closeTicket">
                                    Cerrar ticket
                                </SecondaryButton>
                                <DangerButton :disabled="!cancelAllowed || processing.cancelar" @click="cancelTicket">
                                    Cancelar ticket
                                </DangerButton>
                            </div>
                            <p v-if="actionSuccess" class="text-sm text-emerald-600">{{ actionSuccess }}</p>
                            <InputError :message="actionError" />
                        </div>

                        <p v-else class="text-sm text-gray-500">
                            No tienes permisos para cerrar o cancelar este ticket.
                        </p>
                    </div>

                    <div v-else class="mt-6 space-y-6">
                        <div v-if="canOperate" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-semibold text-gray-900">Estado</h4>
                                <span class="text-xs text-gray-500">{{ transicionesEstado.length }} transiciones</span>
                            </div>

                            <div v-if="transicionesEstado.length === 0" class="text-sm text-gray-500">
                                No hay transiciones disponibles para este ticket.
                            </div>

                            <div v-else class="grid gap-4 sm:grid-cols-[1fr,auto] items-end">
                                <div>
                                    <InputLabel for="estado" value="Nuevo estado" />
                                    <select
                                        id="estado"
                                        v-model="estadoForm.estado"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option value="" disabled>Selecciona un estado</option>
                                        <option v-for="transicion in transicionesEstado" :key="transicion.id" :value="transicion.nombre">
                                            {{ transicion.nombre }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="estadoError" />
                                    <p v-if="estadoHint" class="mt-2 text-xs text-amber-600">{{ estadoHint }}</p>
                                </div>
                                <PrimaryButton :disabled="processing.estado" @click="updateEstado">
                                    Actualizar estado
                                </PrimaryButton>
                            </div>
                        </div>

                        <form v-if="canEditOperativo" class="space-y-4" @submit.prevent="updateOperativo">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-semibold text-gray-900">Campos operativos</h4>
                                <span class="text-xs text-gray-500">Segun tu rol</span>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-if="canAssign">
                                    <InputLabel for="responsable" value="Responsable" />
                                    <select
                                        id="responsable"
                                        v-model="operativoForm.responsable_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option value="">Sin responsable</option>
                                        <option v-for="responsable in catalogs.responsables" :key="responsable.id" :value="responsable.id">
                                            {{ responsable.nombre }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="fieldError(operativoErrors, 'responsable_id')" />
                                </div>

                                <div v-if="canCoordinatorFields">
                                    <InputLabel for="prioridad" value="Prioridad" />
                                    <select
                                        id="prioridad"
                                        v-model="operativoForm.prioridad_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option value="">Sin prioridad</option>
                                        <option v-for="prioridad in catalogs.prioridades" :key="prioridad.id" :value="prioridad.id">
                                            {{ prioridad.nombre }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="fieldError(operativoErrors, 'prioridad_id')" />
                                </div>

                                <div v-if="canCoordinatorFields">
                                    <InputLabel for="sistema" value="Aplicacion" />
                                    <select
                                        id="sistema"
                                        v-model="operativoForm.sistema_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option v-for="sistema in catalogs.sistemas" :key="sistema.id" :value="sistema.id">
                                            {{ sistema.nombre }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="fieldError(operativoErrors, 'sistema_id')" />
                                </div>

                                <div v-if="canCoordinatorFields">
                                    <InputLabel for="fecha_compromiso" value="Fecha compromiso" />
                                    <TextInput
                                        id="fecha_compromiso"
                                        v-model="operativoForm.fecha_compromiso"
                                        type="date"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="fieldError(operativoErrors, 'fecha_compromiso')" />
                                </div>

                                <div v-if="canSoporteFields">
                                    <InputLabel for="tipo" value="Tipo de solicitud" />
                                    <select
                                        id="tipo"
                                        v-model="operativoForm.tipo_solicitud_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option value="">Sin tipo</option>
                                        <option v-for="tipo in catalogs.tipos_solicitud" :key="tipo.id" :value="tipo.id">
                                            {{ tipo.nombre }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="fieldError(operativoErrors, 'tipo_solicitud_id')" />
                                </div>

                                <div v-if="canSoporteFields">
                                    <InputLabel for="fecha_entrega" value="Fecha entrega" />
                                    <TextInput
                                        id="fecha_entrega"
                                        v-model="operativoForm.fecha_entrega"
                                        type="date"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="fieldError(operativoErrors, 'fecha_entrega')" />
                                </div>
                            </div>

                            <div v-if="canSoporteFields">
                                <InputLabel for="resolucion" value="Resolucion" />
                                <textarea
                                    id="resolucion"
                                    v-model="operativoForm.resolucion"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    rows="3"
                                />
                                <InputError class="mt-2" :message="fieldError(operativoErrors, 'resolucion')" />
                            </div>

                            <div class="flex items-center gap-3">
                                <PrimaryButton :disabled="processing.operativo">
                                    Guardar cambios
                                </PrimaryButton>
                                <InputError :message="fieldError(operativoErrors, 'operacion')" />
                            </div>
                        </form>

                        <div v-if="canCloseCancel" class="flex flex-wrap gap-3 border-t border-gray-200 pt-6">
                            <SecondaryButton :disabled="!closeAllowed || processing.cerrar" @click="closeTicket">
                                Cerrar ticket
                            </SecondaryButton>
                            <DangerButton :disabled="!cancelAllowed || processing.cancelar" @click="cancelTicket">
                                Cancelar ticket
                            </DangerButton>
                            <p v-if="actionSuccess" class="text-sm text-emerald-600 w-full">{{ actionSuccess }}</p>
                            <InputError :message="actionError" />
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mt-6 space-y-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Seguimiento del ticket</h3>
                        <p v-if="collaborationSuccess" class="text-sm text-emerald-600">{{ collaborationSuccess }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <h4 class="text-base font-semibold text-gray-900">Comentarios</h4>

                            <label v-if="isRolInterno" class="inline-flex items-center gap-2 text-sm text-gray-600">
                                <input
                                    v-model="mostrarInternos"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                Mostrar internos
                            </label>
                        </div>

                        <form v-if="canCrearComentario" class="space-y-4" @submit.prevent="submitComentario">
                            <div>
                                <InputLabel for="comentario" value="Nuevo comentario" />
                                <textarea
                                    id="comentario"
                                    v-model="comentarioForm.cuerpo"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    rows="3"
                                />
                                <InputError class="mt-2" :message="fieldError(comentarioErrors, 'cuerpo')" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-if="canCrearComentarioInterno">
                                    <InputLabel for="visibilidad" value="Visibilidad" />
                                    <select
                                        id="visibilidad"
                                        v-model="comentarioForm.visibilidad"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option value="publico">Publico</option>
                                        <option value="interno">Interno</option>
                                    </select>
                                    <InputError class="mt-2" :message="fieldError(comentarioErrors, 'visibilidad')" />
                                </div>

                                <div>
                                    <InputLabel for="comentario_archivos" value="Adjuntar evidencia (opcional)" />
                                    <input
                                        id="comentario_archivos"
                                        ref="comentarioArchivosInput"
                                        type="file"
                                        multiple
                                        accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx,.txt"
                                        class="mt-1 block w-full text-sm text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                                        @change="onComentarioArchivosChange"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        Maximo 10 MB. Tipos: pdf, png, jpg, jpeg, docx, xlsx, txt.
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Por ahora solo se lista el archivo; descarga no disponible.
                                    </p>
                                    <p v-if="comentarioForm.archivos.length" class="mt-1 text-xs text-gray-500">
                                        {{ comentarioForm.archivos.length }} archivo(s) seleccionado(s).
                                    </p>
                                    <InputError class="mt-2" :message="comentarioAdjuntoError" />
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <PrimaryButton :disabled="comentarioProcessing">
                                    Agregar comentario
                                </PrimaryButton>
                                <InputError :message="comentarioSubmitError" />
                            </div>
                        </form>

                        <div v-else class="text-sm text-gray-500">
                            No tienes permisos para agregar comentarios en este ticket.
                        </div>

                        <div v-if="comentariosLoading" class="text-sm text-gray-500">
                            Cargando comentarios...
                        </div>
                        <InputError v-else-if="comentariosError" :message="comentariosError" />

                        <div v-else-if="visibleComentarios.length === 0" class="text-sm text-gray-500">
                            No hay comentarios aun.
                        </div>

                        <ul v-else class="space-y-4">
                            <li
                                v-for="comentario in visibleComentarios"
                                :key="comentario.id"
                                class="rounded-lg border border-gray-200 p-4 space-y-3"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ comentario.autor?.nombre || 'Usuario' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ formatDate(comentario.created_at) }}
                                        </div>
                                    </div>
                                    <span
                                        v-if="comentario.visibilidad === 'interno'"
                                        class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700"
                                    >
                                        Interno
                                    </span>
                                </div>

                                <div class="whitespace-pre-line text-sm text-gray-900">
                                    {{ comentario.cuerpo }}
                                </div>

                                <div v-if="comentario.adjuntos?.length" class="space-y-2">
                                    <div class="text-xs font-semibold text-gray-500">Adjuntos</div>
                                    <ul class="space-y-1">
                                        <li
                                            v-for="adjunto in comentario.adjuntos"
                                            :key="adjunto.id"
                                            class="flex flex-wrap items-center justify-between gap-2 text-sm text-gray-700"
                                        >
                                            <span class="truncate">{{ adjunto.nombre_archivo }}</span>
                                            <span class="text-xs text-gray-500">
                                                {{ adjunto.cargado_por?.nombre || 'Usuario' }} · {{ formatDate(adjunto.created_at) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="border-t border-gray-200 pt-6 space-y-4">
                        <h4 class="text-base font-semibold text-gray-900">Involucrados</h4>

                        <form
                            v-if="canGestionarInvolucrados"
                            class="grid gap-3 sm:grid-cols-[1fr,auto] items-end"
                            @submit.prevent="addInvolucrado"
                        >
                            <div>
                                <InputLabel for="involucrado" value="Agregar usuario" />
                                <select
                                    id="involucrado"
                                    v-model="involucradoForm.usuario_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                >
                                    <option value="" disabled>Selecciona un usuario</option>
                                    <option v-for="usuario in catalogs.usuarios || []" :key="usuario.id" :value="usuario.id">
                                        {{ usuario.nombre }} ({{ usuario.email }})
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="fieldError(involucradoErrors, 'usuario_id')" />
                                <InputError class="mt-2" :message="involucradoSubmitError" />
                            </div>
                            <PrimaryButton :disabled="involucradoProcessing">
                                Agregar
                            </PrimaryButton>
                        </form>

                        <div v-if="involucradosLoading" class="text-sm text-gray-500">
                            Cargando involucrados...
                        </div>
                        <InputError v-else-if="involucradosError" :message="involucradosError" />

                        <div v-else-if="involucrados.length === 0" class="text-sm text-gray-500">
                            No hay involucrados.
                        </div>

                        <ul v-else class="space-y-2">
                            <li
                                v-for="item in involucrados"
                                :key="item.id"
                                class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-gray-200 px-3 py-2"
                            >
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ item.usuario?.nombre || 'Usuario' }}</div>
                                    <div class="text-xs text-gray-500">{{ item.usuario?.email }}</div>
                                </div>
                                <DangerButton
                                    v-if="canGestionarInvolucrados"
                                    :disabled="removingInvolucrado[item.usuario?.id] || false"
                                    @click="removeInvolucrado(item.usuario?.id)"
                                >
                                    Remover
                                </DangerButton>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mt-6 space-y-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Trazabilidad</h3>
                        <SecondaryButton @click="refreshTrazabilidad">
                            Refrescar
                        </SecondaryButton>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-base font-semibold text-gray-900">Historial</h4>

                        <div v-if="historialLoading" class="text-sm text-gray-500">
                            Cargando historial...
                        </div>
                        <InputError v-else-if="historialError" :message="historialError" />

                        <div v-else-if="historial.length === 0" class="text-sm text-gray-500">
                            Sin eventos.
                        </div>

                        <ul v-else class="space-y-2">
                            <li
                                v-for="evento in historial"
                                :key="evento.id"
                                class="rounded-md border border-gray-200 px-3 py-2"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ auditSummary(evento) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ evento.actor?.nombre || 'Usuario' }} · {{ formatDate(evento.created_at) }}
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <div v-if="historialHasMore" class="pt-2">
                            <SecondaryButton :disabled="historialLoading" @click="loadMoreHistorial">
                                Cargar más
                            </SecondaryButton>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6 space-y-4">
                        <h4 class="text-base font-semibold text-gray-900">Relaciones</h4>

                        <form class="grid gap-3 sm:grid-cols-[1fr,auto] items-end" @submit.prevent="submitRelacion">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <InputLabel for="ticket_relacionado_id" value="Ticket relacionado (id)" />
                                    <TextInput
                                        id="ticket_relacionado_id"
                                        v-model="relacionForm.ticket_relacionado_id"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="1"
                                    />
                                    <InputError class="mt-2" :message="fieldError(relacionErrors, 'ticket_relacionado_id')" />
                                </div>

                                <div>
                                    <InputLabel for="tipo_relacion" value="Tipo relación" />
                                    <select
                                        id="tipo_relacion"
                                        v-model="relacionForm.tipo_relacion"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                                    >
                                        <option value="relacionado">Relacionado</option>
                                        <option value="reabre">Reabre</option>
                                        <option v-if="canCloseCancel" value="duplicado_de">Duplicado de</option>
                                    </select>
                                    <InputError class="mt-2" :message="fieldError(relacionErrors, 'tipo_relacion')" />
                                </div>
                            </div>

                            <PrimaryButton :disabled="relacionProcessing">
                                Crear
                            </PrimaryButton>
                        </form>

                        <InputError v-if="relacionSubmitError" :message="relacionSubmitError" />

                        <div v-if="relacionesLoading" class="text-sm text-gray-500">
                            Cargando relaciones...
                        </div>
                        <InputError v-else-if="relacionesError" :message="relacionesError" />

                        <div v-else-if="relaciones.length === 0" class="text-sm text-gray-500">
                            No hay relaciones.
                        </div>

                        <ul v-else class="space-y-2">
                            <li
                                v-for="relacion in relaciones"
                                :key="relacion.id"
                                class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-gray-200 px-3 py-2"
                            >
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        Ticket #{{ relationOtherTicket(relacion)?.id }} · {{ relationOtherTicket(relacion)?.asunto || 'Sin asunto' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ relacion.tipo_relacion }} · {{ relacion.creado_por?.nombre || 'Usuario' }} ·
                                        {{ formatDate(relacion.created_at) }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="border-t border-gray-200 pt-6 space-y-4">
                        <h4 class="text-base font-semibold text-gray-900">Tiempo</h4>

                        <div v-if="!canManageTiempo" class="text-sm text-gray-500">
                            Solo roles internos autorizados pueden ver y registrar tiempo.
                        </div>

                        <template v-else>
                            <form class="grid gap-3 sm:grid-cols-[1fr,auto] items-end" @submit.prevent="submitTiempo">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <InputLabel for="minutos" value="Minutos" />
                                        <TextInput
                                            id="minutos"
                                            v-model="tiempoForm.minutos"
                                            type="number"
                                            class="mt-1 block w-full"
                                            min="1"
                                        />
                                        <InputError class="mt-2" :message="fieldError(tiempoErrors, 'minutos')" />
                                    </div>

                                    <div>
                                        <InputLabel for="nota_tiempo" value="Nota (opcional)" />
                                        <TextInput
                                            id="nota_tiempo"
                                            v-model="tiempoForm.nota"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError class="mt-2" :message="fieldError(tiempoErrors, 'nota')" />
                                    </div>
                                </div>

                                <PrimaryButton :disabled="tiempoProcessing">
                                    Registrar
                                </PrimaryButton>
                            </form>

                            <InputError v-if="tiempoSubmitError" :message="tiempoSubmitError" />

                            <div v-if="tiempoLoading" class="text-sm text-gray-500">
                                Cargando tiempo...
                            </div>
                            <InputError v-else-if="tiempoError" :message="tiempoError" />

                            <div v-else-if="tiempoRegistros.length === 0" class="text-sm text-gray-500">
                                Sin registros de tiempo.
                            </div>

                            <ul v-else class="space-y-2">
                                <li
                                    v-for="registro in tiempoRegistros"
                                    :key="registro.id"
                                    class="rounded-md border border-gray-200 px-3 py-2"
                                >
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ registro.minutos }} min
                                            <span v-if="registro.nota" class="text-gray-600 font-normal">
                                                · {{ registro.nota }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ registro.autor?.nombre || 'Usuario' }} · {{ formatDate(registro.created_at) }}
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div v-if="tiempoHasMore" class="pt-2">
                                <SecondaryButton :disabled="tiempoLoading" @click="loadMoreTiempo">
                                    Cargar más
                                </SecondaryButton>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
