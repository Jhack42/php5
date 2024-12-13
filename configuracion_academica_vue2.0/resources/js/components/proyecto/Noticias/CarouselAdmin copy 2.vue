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
            <tr v-for="item in items" :key="item.ID" class="ca-tr">
              <td class="ca-td">
                <div class="ca-order">
                  <button @click="moveItem(item, -1)" :disabled="isFirstItem(item)" class="ca-order__btn">↑</button>
                  <span class="ca-order__num">{{ item.DISPLAY_ORDER }}</span>
                  <button @click="moveItem(item, 1)" :disabled="isLastItem(item)" class="ca-order__btn">↓</button>
                </div>
              </td>
              <td class="ca-td">{{ item.NAME }}</td>
              <td class="ca-td ca-td--desc">{{ item.DESCRIPTION }}</td>
              <td class="ca-td">{{ formatDate(item.START_DATE) }}</td>
              <td class="ca-td">{{ formatDate(item.END_DATE) }}</td>
              <td class="ca-td">
                <button @click="toggleStatus(item)"
                  class="ca-status"
                  :class="{
                    'ca-status--active': item.IS_ACTIVE,
                    'ca-status--inactive': !item.IS_ACTIVE
                  }">
                  {{ item.IS_ACTIVE ? 'Activo' : 'Inactivo' }}
                </button>
              </td>
              <td class="ca-td">
                <div class="ca-actions-cell">
                  <button @click="previewItem(item)" class="ca-act ca-act--view">Visualizar</button>
                  <button @click="editItem(item)" class="ca-act ca-act--edit">Editar</button>
                  <button @click="deleteItem(item.ID)" class="ca-act ca-act--del">Eliminar</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

            <!-- Panel derecho: Preview del item -->
            <!-- Preview Panel -->
            <div v-if="showPreview" class="ca-preview">
                <div class="ca-preview__header">
                    <div class="ca-preview__tabs">
                        <button @click="previewMode = 'render'" class="ca-preview__tab"
                            :class="{ 'ca-preview__tab--active': previewMode === 'render' }">
                            Preview
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
                        <div v-html="selectedItem?.HTML_CONTENT"></div>
                    </div>
                    <pre v-else class="ca-preview__code">
            <code>{{ selectedItem?.HTML_CONTENT }}</code>
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
                            <input v-model="currentItem.NAME" class="ca-form__input" required>
                        </div>

                        <div class="ca-form__group ca-form__group--full">
                            <label class="ca-form__label">Descripción:</label>
                            <textarea v-model="currentItem.DESCRIPTION" class="ca-form__textarea" rows="2"></textarea>
                        </div>

                        <div class="ca-form__group ca-form__group--full">
                            <label class="ca-form__label">Contenido HTML:</label>
                            <textarea v-model="currentItem.HTML_CONTENT"
                                class="ca-form__textarea ca-form__textarea--code" rows="10" required></textarea>
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Fecha Inicio:</label>
                            <input type="datetime-local" v-model="currentItem.START_DATE" class="ca-form__input">
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Fecha Fin:</label>
                            <input type="datetime-local" v-model="currentItem.END_DATE" class="ca-form__input">
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Categoría:</label>
                            <input v-model="currentItem.CATEGORY" class="ca-form__input">
                        </div>

                        <div class="ca-form__group">
                            <label class="ca-form__label">Efecto de Transición:</label>
                            <select v-model="currentItem.TRANSITION_EFFECT" class="ca-form__select">
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

export default {
    setup() {
        const API_BASE = 'http://127.0.0.1:8000/api/carousel';
        const socket = io('http://localhost:3000');

        const items = ref([]);
        const currentItem = ref(getEmptyItem());
        const editingId = ref(null);
        const showPreview = ref(false);
        const selectedItem = ref(null);
        const previewMode = ref('render');
        const showPreviewModal = ref(false);
        const showCreateModal = ref(false);

        function getEmptyItem() {
            return {
                NAME: '',
                DESCRIPTION: '',
                HTML_CONTENT: '',
                START_DATE: '',
                END_DATE: '',
                IS_ACTIVE: '1',
                CATEGORY: '',
                TRANSITION_EFFECT: 'fade',
                DISPLAY_ORDER: '0',
                BACKGROUND_COLOR: '',
                CUSTOM_STYLES: '',
                MOBILE_HTML_CONTENT: '',
                METADATA: {}
            };
        }

        async function loadItems() {
            try {
                const response = await axios.get(API_BASE);
                if (response.data.success) {
                    items.value = response.data.data;
                }
            } catch (error) {
                console.error('Error al cargar items:', error);
            }
        }

        async function saveItem() {
            try {
                const itemData = {
                    ...currentItem.value,
                    IS_ACTIVE: currentItem.value.IS_ACTIVE.toString()
                };

                let response;
                if (editingId.value) {
                    response = await axios.put(`${API_BASE}/${editingId.value}`, itemData);
                    if (response.data.success) {
                        socket.emit('carousel:update', response.data.data);
                    }
                } else {
                    response = await axios.post(API_BASE, itemData);
                    if (response.data.success) {
                        socket.emit('carousel:create', response.data.data);
                    }
                }
                closeModal();
            } catch (error) {
                console.error('Error al guardar item:', error);
            }
        }

        function editItem(item) {
            currentItem.value = {
                NAME: item.NAME,
                DESCRIPTION: item.DESCRIPTION,
                HTML_CONTENT: item.HTML_CONTENT || '',
                START_DATE: item.START_DATE || '',
                END_DATE: item.END_DATE || '',
                IS_ACTIVE: item.IS_ACTIVE.toString(),
                CATEGORY: item.CATEGORY || '',
                TRANSITION_EFFECT: item.TRANSITION_EFFECT || 'fade',
                DISPLAY_ORDER: item.DISPLAY_ORDER.toString(),
                BACKGROUND_COLOR: item.BACKGROUND_COLOR || '',
                CUSTOM_STYLES: item.CUSTOM_STYLES || '',
                MOBILE_HTML_CONTENT: item.MOBILE_HTML_CONTENT || '',
                METADATA: item.METADATA || {}
            };
            editingId.value = item.ID;
            showCreateModal.value = true;
        }

        async function deleteItem(id) {
            if (confirm('¿Estás seguro de eliminar este item?')) {
                try {
                    const response = await axios.delete(`${API_BASE}/${id}`);
                    if (response.data.success) {
                        socket.emit('carousel:delete', id);
                    }
                } catch (error) {
                    console.error('Error al eliminar item:', error);
                }
            }
        }

        async function toggleStatus(item) {
            try {
                const newStatus = item.IS_ACTIVE === '1' ? '0' : '1';
                const response = await axios.put(`${API_BASE}/${item.ID}`, {
                    ...item,
                    IS_ACTIVE: newStatus
                });
                if (response.data.success) {
                    socket.emit('carousel:update', response.data.data);
                }
            } catch (error) {
                console.error('Error al cambiar estado:', error);
            }
        }

        async function moveItem(item, direction) {
            try {
                const newOrder = parseInt(item.DISPLAY_ORDER) + direction;
                const response = await axios.post(`${API_BASE}/reorder`, {
                    items: [item.ID],
                    newOrder: newOrder.toString()
                });
                if (response.data.success) {
                    socket.emit('carousel:reorder', response.data.data);
                }
            } catch (error) {
                console.error('Error al mover item:', error);
            }
        }

        function isFirstItem(item) {
            return parseInt(item.DISPLAY_ORDER) === Math.min(...items.value.map(i => parseInt(i.DISPLAY_ORDER)));
        }

        function isLastItem(item) {
            return parseInt(item.DISPLAY_ORDER) === Math.max(...items.value.map(i => parseInt(i.DISPLAY_ORDER)));
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
            showCreateModal.value = false;
            currentItem.value = getEmptyItem();
            editingId.value = null;
        }

        function formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString();
        }

        onMounted(() => {
            loadItems();

            socket.on('connect', () => {
                console.log('Conectado al WebSocket:', socket.id);
            });

            socket.on('carousel:updated', (data) => {
                const index = items.value.findIndex(item => item.ID === data.ID);
                if (index !== -1) {
                    items.value[index] = data;
                }
            });

            socket.on('carousel:created', (data) => {
                items.value.push(data);
                items.value.sort((a, b) => parseInt(a.DISPLAY_ORDER) - parseInt(b.DISPLAY_ORDER));
            });

            socket.on('carousel:deleted', (id) => {
                const index = items.value.findIndex(item => item.ID === id);
                if (index !== -1) {
                    items.value.splice(index, 1);
                }
            });

            socket.on('carousel:reordered', (data) => {
                loadItems(); // Recargar toda la lista para asegurar el orden correcto
            });

            socket.on('disconnect', () => {
                console.log('Desconectado del WebSocket');
            });

            socket.on('error', (error) => {
                console.error('Error de WebSocket:', error);
            });
        });

        onUnmounted(() => {
            socket.disconnect();
        });

        return {
            items,
            currentItem,
            editingId,
            showPreview,
            selectedItem,
            previewMode,
            showPreviewModal,
            showCreateModal,
            saveItem,
            editItem,
            deleteItem,
            toggleStatus,
            moveItem,
            isFirstItem,
            isLastItem,
            previewItem,
            closePreview,
            togglePreviewModal,
            closeModal,
            formatDate
        };
    }
};
</script>
<style lang="scss" src="../../../../scss/crud_admin.scss"></style>
