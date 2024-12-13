<template>
    <div class="ca">
        <!-- Header -->
        <div class="ca-header">
            <h1 class="ca-title">Administrador de Carrusel</h1>
            <div class="ca-actions">
                <button @click="togglePreviewModal" class="ca-btn ca-btn--preview">
                    Preview Final
                </button>
                <button @click="showCreateModal = true" class="ca-btn ca-btn--create">
                    Nuevo Item
                </button>
            </div>
        </div>
        <div class="toast-container">
            <div v-for="toast in toasts" :key="toast.id" class="toast" :class="{
                'toast--success': toast.type === 'success',
                'toast--error': toast.type === 'error',
                'toast--warning': toast.type === 'warning'
            }" @click="removeToast(toast.id)">
                <div class="toast__content">
                    <div class="toast__title">{{ toast.title }}</div>
                    <div class="toast__message">{{ toast.message }}</div>
                </div>
                <button class="toast__close" @click.stop="removeToast(toast.id)">&times;</button>
            </div>
        </div>
        <!-- Tabla CRUD -->
        <div class="ca-main" :class="{ 'ca-main--preview': showPreview }">
            <div class="ca-table-wrap">
                <table class="ca-table">
                    <thead class="ca-thead">
                        <tr>
                            <th class="ca-th">Orden</th>
                            <th class="ca-th">Nombre</th>
                            <th class="ca-th">Descripción</th>
                            <th class="ca-th">Fecha Inicio</th>
                            <th class="ca-th">Fecha Fin</th>
                            <th class="ca-th">Estado</th>
                            <th class="ca-th">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="ca-tbody">
                        <tr v-for="item in items" :key="item.id" :class="getRowClasses(item)">
                            <td class="ca-td">
                                <div class="ca-order">
                                    <button @click="moveItem(item, -1)" :disabled="isFirstItem(item)"
                                        class="ca-order__btn">↑</button>
                                    <span class="ca-order__num">{{ item.display_order }}</span>
                                    <button @click="moveItem(item, 1)" :disabled="isLastItem(item)"
                                        class="ca-order__btn">↓</button>
                                </div>
                            </td>
                            <td class="ca-td">{{ item.name }}</td>
                            <td class="ca-td ca-td--desc">{{ item.description }}</td>
                            <td class="ca-td">{{ formatDate(item.start_date) }}</td>
                            <td class="ca-td">{{ formatDate(item.end_date) }}</td>
                            <td class="ca-td">
                                <button @click="toggleStatus(item)" class="ca-status" :class="{
                                    'ca-status--active': item.is_active,
                                    'ca-status--inactive': !item.is_active
                                }">
                                    {{ item.is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="ca-td">
                                <div class="ca-actions-cell">
                                    <button @click="previewItem(item)" class="ca-act ca-act--view">Visualizar</button>
                                    <button @click="editItem(item)" class="ca-act ca-act--edit">Editar</button>
                                    <button @click="deleteItem(item.id)" class="ca-act ca-act--del">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Preview Panel -->
            <div v-if="showPreview" class="ca-preview">
                <!-- Actualiza la sección del preview en tu template -->
                <div class="ca-preview__header">
                    <div class="ca-preview__tabs">
                        <button @click="previewMode = 'render'" class="ca-preview__tab"
                            :class="{ 'ca-preview__tab--active': previewMode === 'render' }">
                            Preview
                        </button>
                        <button @click="previewMode = 'edit'" class="ca-preview__tab"
                            :class="{ 'ca-preview__tab--active': previewMode === 'edit' }">
                            Editor Visual
                        </button>
                        <button @click="previewMode = 'code'" class="ca-preview__tab"
                            :class="{ 'ca-preview__tab--active': previewMode === 'code' }">
                            Código
                        </button>
                    </div>
                    <button @click="closePreview" class="ca-preview__close">&times;</button>
                </div>
                <div class="ca-preview__content">
                    <div v-if="previewMode === 'render'" class="ca-preview__render">
                        <div v-html="selectedItem?.html_content"></div>
                    </div>
                    <div v-else-if="previewMode === 'edit'" class="ca-preview__edit">
                        <VisualEditor v-model="selectedItem.html_content" @update:modelValue="handleContentUpdate" />
                        <div class="ca-preview__edit-actions">
                            <button @click="saveVisualEditorContent" class="ca-btn ca-btn--save">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                    <pre v-else class="ca-preview__code">
        <code>{{ selectedItem?.html_content }}</code>
    </pre>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal" class="ca-modal">
            <div class="ca-modal__box">
                <div class="ca-modal__header">
                    <h3 class="ca-modal__title">{{ editingId ? 'Editar' : 'Crear' }} Item</h3>
                    <button @click="closeModal" class="ca-modal__close">&times;</button>
                </div>

                <form @submit.prevent="saveItem" class="ca-form">
                    <div class="ca-form__grid">
                        <div class="ca-form__group ca-form__group--full">
                            <label class="ca-form__label">Nombre:</label>
                            <input v-model="currentItem.name" class="ca-form__input" required>
                        </div>

                        <div class="ca-form__group ca-form__group--full">
                            <label class="ca-form__label">Descripción:</label>
                            <textarea v-model="currentItem.description" class="ca-form__textarea" rows="2"></textarea>
                        </div>

                        <div class="ca-form__group ca-form__group--full">
                            <label class="ca-form__label">Contenido HTML:</label>
                            <textarea v-model="currentItem.html_content"
                                class="ca-form__textarea ca-form__textarea--code" rows="10" required></textarea>
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Fecha Inicio:</label>
                            <input type="datetime-local" v-model="currentItem.start_date" class="ca-form__input">
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Fecha Fin:</label>
                            <input type="datetime-local" v-model="currentItem.end_date" class="ca-form__input">
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Categoría:</label>
                            <input v-model="currentItem.category" class="ca-form__input">
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Efecto de Transición:</label>
                            <select v-model="currentItem.transition_effect" class="ca-form__select">
                                <option value="fade">Fade</option>
                                <option value="slide">Slide</option>
                                <option value="zoom">Zoom</option>
                            </select>
                        </div>
                    </div>

                    <div class="ca-form__actions">
                        <button type="button" @click="closeModal" class="ca-form__btn ca-form__btn--cancel">
                            Cancelar
                        </button>
                        <button type="submit" class="ca-form__btn ca-form__btn--submit">
                            {{ editingId ? 'Actualizar' : 'Crear' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Modal -->
        <div v-if="showPreviewModal" class="ca-modal ca-modal--full">
            <button @click="togglePreviewModal" class="ca-modal__close ca-modal__close--light">&times;</button>
            <iframe src="http://127.0.0.1:8000/noticias" class="ca-modal__frame"></iframe>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { io } from 'socket.io-client';
import axios from 'axios';
import VisualEditor from './components/VisualEditor.vue';

export default {
    components: {
        VisualEditor
    },

    setup() {
        const API_BASE = 'http://127.0.0.1:8000/api/v1/carousel';
        const editingUsers = ref({});
        const socket = io('http://localhost:3000');

        // Estado del componente
        const items = ref([]);
        const currentItem = ref(getEmptyItem());
        const editingId = ref(null);
        const showPreview = ref(false);
        const selectedItem = ref(null);
        const previewMode = ref('render');
        const showPreviewModal = ref(false);
        const showCreateModal = ref(false);
        const toasts = ref([]);
        let toastId = 0;

        // Agregar nuevas clases CSS dinámicas a la fila
        function getRowClasses(item) {
            return {
                'ca-tr': true,
                'ca-tr--editing': isBeingEditedByOthers(item.id),
                'ca-tr--just-updated': isRecentlyUpdated(item.id),
            };
        }

        // Verificar si el item está siendo editado por otros
        function isBeingEditedByOthers(itemId) {
            return editingUsers.value[itemId] &&
                editingUsers.value[itemId].userId !== socket.id;
        }

        // Verificar si el item fue actualizado recientemente
        function isRecentlyUpdated(itemId) {
            const updateInfo = editingUsers.value[itemId];
            if (!updateInfo || updateInfo.userId === socket.id) return false;

            const RECENT_UPDATE_THRESHOLD = 3000;
            return updateInfo.type === 'update' &&
                Date.now() - updateInfo.timestamp < RECENT_UPDATE_THRESHOLD;
        }

        // Funciones de utilidad
        function getEmptyItem() {
            return {
                name: '',
                description: '',
                html_content: '',
                start_date: '',
                end_date: '',
                is_active: true,
                category: '',
                transition_effect: 'fade',
                display_order: 0,
                background_color: '',
                custom_styles: '',
                mobile_html_content: '',
                metadata: {}
            };
        }

        function addToast(title, message, type = 'success', duration = 5000) {
            const id = ++toastId;
            toasts.value.push({ id, title, message, type });
            setTimeout(() => removeToast(id), duration);
        }

        function removeToast(id) {
            const index = toasts.value.findIndex(t => t.id === id);
            if (index > -1) {
                toasts.value.splice(index, 1);
            }
        }

        // Funciones CRUD
        async function loadItems() {
            try {
                const response = await axios.get(API_BASE);
                if (response.data.success) {
                    items.value = response.data.data;
                } else {
                    addToast('Error', 'No se pudieron cargar los items', 'error');
                }
            } catch (error) {
                addToast('Error', 'Error al cargar los items', 'error');
                console.error('Error al cargar items:', error);
            }
        }

        async function saveItem() {
            try {
                let response;
                const itemData = {
                    ...currentItem.value,
                    is_active: Boolean(currentItem.value.is_active),
                    display_order: parseInt(currentItem.value.display_order) || 0,
                    metadata: JSON.stringify(currentItem.value.metadata)
                };

                if (editingId.value) {
                    response = await axios.put(`${API_BASE}/${editingId.value}`, itemData);
                    if (response.data.success) {
                        addToast('Éxito', 'Item actualizado correctamente', 'success');
                        socket.emit('carousel:update', response.data.data);
                        socket.emit('carousel:editing:update', {
                            itemId: editingId.value,
                            userId: socket.id,
                            timestamp: Date.now()
                        });
                    }
                } else {
                    response = await axios.post(API_BASE, itemData);
                    if (response.data.success) {
                        addToast('Éxito', 'Item creado correctamente', 'success');
                        socket.emit('carousel:create', response.data.data);
                        socket.emit('carousel:editing:update', {
                            itemId: response.data.data.id,
                            userId: socket.id,
                            timestamp: Date.now()
                        });
                    }
                }

                await loadItems();
                closeModal();
            } catch (error) {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    let errorMessage = 'Por favor corrige los siguientes errores:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `- ${errors[key][0]}\n`;
                    });
                    addToast('Error de Validación', errorMessage, 'warning', 8000);
                } else {
                    addToast('Error',
                        error.response?.data?.message || 'Error al guardar el item',
                        'error');
                }
                console.error('Error al guardar item:', error);
            }
        }

        async function deleteItem(id) {
            if (confirm('¿Estás seguro de eliminar este item?')) {
                try {
                    const response = await axios.delete(`${API_BASE}/${id}`);
                    if (response.data.success) {
                        addToast('Éxito', 'Item eliminado correctamente', 'success');
                        socket.emit('carousel:delete', id);
                        await loadItems();
                    }
                } catch (error) {
                    addToast('Error', 'No se pudo eliminar el item', 'error');
                    console.error('Error al eliminar item:', error);
                }
            }
        }

        function editItem(item) {
            currentItem.value = {
                name: item.name,
                description: item.description,
                html_content: item.html_content || '',
                start_date: item.start_date || '',
                end_date: item.end_date || '',
                is_active: item.is_active,
                category: item.category || '',
                transition_effect: item.transition_effect || 'fade',
                display_order: item.display_order,
                background_color: item.background_color || '',
                custom_styles: item.custom_styles || '',
                mobile_html_content: item.mobile_html_content || '',
                metadata: item.metadata || {}
            };
            editingId.value = item.id;
            showCreateModal.value = true;
            socket.emit('carousel:editing:start', {
                itemId: item.id,
                userId: socket.id
            });
        }

        async function toggleStatus(item) {
            try {
                const response = await axios.put(`${API_BASE}/${item.id}`, {
                    ...item,
                    is_active: !item.is_active
                });
                if (response.data.success) {
                    addToast('Éxito', `Item ${!item.is_active ? 'activado' : 'desactivado'} correctamente`, 'success');
                    socket.emit('carousel:update', response.data.data);
                    await loadItems();
                }
            } catch (error) {
                addToast('Error', 'No se pudo cambiar el estado del item', 'error');
                console.error('Error al cambiar estado:', error);
            }
        }

        async function moveItem(item, direction) {
            const newOrder = item.display_order + direction;
            try {
                const response = await axios.put(`${API_BASE}/${item.id}`, {
                    ...item,
                    display_order: newOrder
                });
                if (response.data.success) {
                    socket.emit('carousel:update', response.data.data);
                    await loadItems();
                }
            } catch (error) {
                addToast('Error', 'No se pudo mover el item', 'error');
                console.error('Error al mover item:', error);
            }
        }

        // Funciones del Editor Visual
        function handleContentUpdate(newContent) {
            if (selectedItem.value) {
                selectedItem.value.html_content = newContent;
            }
        }

        function saveVisualEditorContent() {
            if (selectedItem.value) {
                currentItem.value = selectedItem.value;
                editingId.value = selectedItem.value.id;
                saveItem();
            }
        }

        // Funciones auxiliares
        function isFirstItem(item) {
            return item.display_order === Math.min(...items.value.map(i => i.display_order));
        }

        function isLastItem(item) {
            return item.display_order === Math.max(...items.value.map(i => i.display_order));
        }

        function previewItem(item) {
            selectedItem.value = item;
            showPreview.value = true;
            previewMode.value = 'render';
        }

        function closePreview() {
            showPreview.value = false;
            selectedItem.value = null;
        }

        function togglePreviewModal() {
            showPreviewModal.value = !showPreviewModal.value;
        }

        function closeModal() {
            if (editingId.value) {
                socket.emit('carousel:editing:end', {
                    itemId: editingId.value,
                    userId: socket.id
                });
            }
            showCreateModal.value = false;
            currentItem.value = getEmptyItem();
            editingId.value = null;
        }

        function formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString();
        }

        // Lifecycle hooks
        onMounted(() => {
            loadItems();

            socket.on('connect', () => {
                console.log('Conectado al WebSocket:', socket.id);
            });

            socket.on('disconnect', () => {
                console.log('Desconectado del WebSocket');
            });

            socket.on('error', (error) => {
                console.error('Error de WebSocket:', error);
                addToast('Error', 'Error de conexión con WebSocket', 'error');
            });

            socket.on('carousel:updated', async () => {
                await loadItems();
            });

            socket.on('carousel:created', async () => {
                await loadItems();
            });

            socket.on('carousel:deleted', async () => {
                await loadItems();
            });

            socket.on('carousel:editing:started', (data) => {
                editingUsers.value[data.itemId] = {
                    userId: data.userId,
                    timestamp: Date.now(),
                    type: 'editing'
                };
            });

            socket.on('carousel:editing:ended', (data) => {
                if (editingUsers.value[data.itemId]?.userId === data.userId) {
                    delete editingUsers.value[data.itemId];
                }
            });

            socket.on('carousel:editing:updated', (data) => {
                editingUsers.value[data.itemId] = {
                    userId: data.userId,
                    timestamp: data.timestamp,
                    type: 'update'
                };

                setTimeout(() => {
                    if (editingUsers.value[data.itemId]?.timestamp === data.timestamp) {
                        delete editingUsers.value[data.itemId];
                    }
                }, 3000);

                loadItems();
            });
        });

        onUnmounted(() => {
            socket.disconnect();
        });

        return {
            // Estado
            items,
            currentItem,
            editingId,
            showPreview,
            selectedItem,
            previewMode,
            showPreviewModal,
            showCreateModal,
            toasts,
            editingUsers,

            // Métodos CRUD
            saveItem,
            editItem,
            deleteItem,
            toggleStatus,
            moveItem,

            // Métodos de navegación y UI
            isFirstItem,
            isLastItem,
            previewItem,
            closePreview,
            togglePreviewModal,
            closeModal,
            formatDate,

            // Métodos de notificación
            addToast,
            removeToast,

            // Métodos de edición colaborativa
            getRowClasses,
            isBeingEditedByOthers,
            isRecentlyUpdated,

            // Métodos del Editor Visual
            handleContentUpdate,
            saveVisualEditorContent
        };
    }
};
</script>

<style lang="scss" src="../../../../scss/crud_admin.scss"></style>
