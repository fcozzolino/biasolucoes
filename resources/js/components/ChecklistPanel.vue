<template>
  <div class="checklist-panel">
    <div class="checklist-header d-flex justify-content-between align-items-center mb-3">
      <h6 class="mb-0">Checklist</h6>
      <button v-if="!inline" class="btn-close" @click="$emit('close')"></button>
    </div>

    <div v-if="loading" class="text-center p-4">
      <div class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <div v-else-if="checklists.length === 0" class="text-center text-muted p-4">
      <p>Nenhuma checklist encontrada.</p>
    </div>

    <div v-else>
      <!-- Cada Checklist -->
      <div
        v-for="(checklist, index) in checklists"
        :key="`checklist-${checklist.id}`"
        class="checklist-container mb-4"
      >
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="checklist-title mb-0 d-flex align-items-center flex-grow-1">
            <i class="fa-solid fa-list-check me-2"></i>

            <!-- Modo visualização -->
            <span
              v-if="editingChecklistId !== checklist.id"
              @click.stop="startEditChecklistTitle(checklist)"
              class="editable-title flex-grow-1"
            >
              {{ checklist.title }}
            </span>

            <!-- Modo edição -->
            <input
              v-else
              v-model="editingChecklistTitle"
              @keyup.enter="saveChecklistTitle(checklist)"
              @keyup.esc="cancelEditChecklistTitle"
              @blur="saveChecklistTitle(checklist)"
              @click.stop
              class="form-control form-control-sm flex-grow-1"
              type="text"
              :ref="el => (checklistTitleInput = el)"
            />
          </h6>

          <div class="checklist-progress">
            <small class="text-muted">
              {{ getCompletedCount(index) }}/{{ getTotalCount(index) }}
            </small>
          </div>
        </div>

        <!-- Barra de Progresso -->
        <div class="progress mb-3" style="height: 6px">
          <div
            class="progress-bar bg-success"
            :style="{ width: getProgress(index) + '%' }"
            :key="`progress-${checklist.id}-${updateKey}`"
          ></div>
        </div>

        <!-- Itens da Checklist -->
        <div
          class="checklist-items"
          @dragover="handleChecklistDragOver($event)"
          @drop="handleChecklistDrop($event, checklist.id)"
          @dragenter.prevent
          @dragleave="handleDragLeave($event)"
        >
          <div
            v-for="(item, itemIndex) in checklist.items"
            :key="`item-${item.id}-${item.is_completed}`"
            class="checklist-item d-flex align-items-center gap-2 mb-2 p-2 rounded"
            :class="{
              'bg-light': item.is_completed,
              dragging: draggedItem?.id === item.id,
            }"
            :data-item-id="item.id"
            :data-item-index="itemIndex"
          >
            <i
              class="fa-solid fa-grip-vertical drag-handle text-muted"
              draggable="true"
              @dragstart="handleDragStart($event, checklist.id, item, itemIndex)"
              @dragend="handleDragEnd"
              @click.stop
              style="cursor: move"
            ></i>
            <input
              type="checkbox"
              class="form-check-input"
              :checked="item.is_completed"
              @change="toggleItem(index, item.id, $event.target.checked)"
              @click.stop
            />

            <!-- Modo visualização -->
            <span
              v-if="editingItemId !== item.id"
              class="flex-grow-1 editable-item-content"
              :class="{ 'text-decoration-line-through text-muted': item.is_completed }"
              @click.stop="startEditItemContent(item)"
            >
              {{ item.content }}
            </span>

            <!-- Modo edição -->
            <input
              v-else
              v-model="editingItemContent"
              @keyup.enter="saveItemContent(checklist.id, item)"
              @keyup.esc="cancelEditItemContent"
              @blur="saveItemContent(checklist.id, item)"
              @click.stop
              class="form-control form-control-sm flex-grow-1"
              :ref="el => (itemContentInput = el)"
            />

            <button
              class="btn btn-sm btn-link text-danger p-0"
              @click.stop="deleteItem(index, item.id)"
            >
              <i class="fa-solid fa-times"></i>
            </button>
          </div>

          <!-- Placeholder para drop -->
          <div
            v-if="isDraggingItem && draggedFromChecklist.value === checklist.id"
            class="drop-placeholder"
            :style="{ height: '40px' }"
          ></div>
        </div>

        <!-- Formulário para Adicionar Item -->
        <div class="add-item-form mt-3">
          <div class="input-group input-group-sm">
            <input
              v-model="newItems[checklist.id]"
              type="text"
              class="form-control"
              placeholder="Adicionar um item..."
              @keyup.enter="addItem(index, checklist.id)"
            />
            <button
              class="btn btn-primary"
              @click="addItem(index, checklist.id)"
              :disabled="!newItems[checklist.id]?.trim()"
            >
              <i class="fa-solid fa-plus"></i>
            </button>
          </div>
        </div>

        <!-- Botão para Excluir Checklist -->
        <div class="text-end mt-2 position-relative">
          <button
            class="btn btn-sm btn-link text-danger"
            @click.stop="toggleDeleteConfirm(checklist.id)"
          >
            Excluir checklist
          </button>

          <!-- Popover de Confirmação -->
          <div
            v-if="showDeleteConfirm === checklist.id"
            class="delete-confirm-popover position-absolute"
            @click.stop
          >
            <div class="arrow"></div>
            <div class="popover-content">
              <p class="mb-2 text-start">Excluir {{ checklist.title }}?</p>
              <p class="text-muted small mb-3 text-start">
                A exclusão de um checklist é permanente e não é possível recuperá-lo.
              </p>
              <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-sm btn-secondary" @click="closeDeleteConfirm">
                  Cancelar
                </button>
                <button
                  class="btn btn-sm btn-danger"
                  @click="executeDeleteChecklist(index, checklist.id)"
                >
                  Excluir
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, reactive, watch, onMounted, nextTick, onBeforeUnmount } from 'vue';
  import axios from 'axios';
  import { useToast } from '@/composables/useToast';

  // Composables
  const { showSuccessToast, showErrorToast, showWarningToast } = useToast();

  const props = defineProps({
    cardId: {
      type: Number,
      required: true,
    },
    inline: {
      type: Boolean,
      default: false,
    },
  });

  const emit = defineEmits(['close', 'updated', 'no-checklists']);

  // Estado reativo
  const checklists = ref([]);
  const loading = ref(true);
  const newItems = reactive({});
  const updateKey = ref(0);
  const showDeleteConfirm = ref(null);
  const draggedItem = ref(null);
  const draggedFromChecklist = ref(null);
  const draggedFromIndex = ref(null);
  const isDraggingItem = ref(false);
  const dropIndicator = ref(null);

  // Controle de edição
  const editingChecklistId = ref(null);
  const editingChecklistTitle = ref('');
  const editingItemId = ref(null);
  const editingItemContent = ref('');
  const checklistTitleInput = ref(null);
  const itemContentInput = ref(null);

  // Força atualização da view
  const forceUpdate = () => {
    updateKey.value++;
  };

  // Métodos de cálculo usando índice para garantir reatividade
  const getProgress = index => {
    const checklist = checklists.value[index];
    if (!checklist || !checklist.items || checklist.items.length === 0) return 0;
    const completed = checklist.items.filter(item => item.is_completed).length;
    const progress = Math.round((completed / checklist.items.length) * 100);
    return progress;
  };

  const getCompletedCount = index => {
    const checklist = checklists.value[index];
    if (!checklist || !checklist.items) return 0;
    return checklist.items.filter(item => item.is_completed).length;
  };

  const getTotalCount = index => {
    const checklist = checklists.value[index];
    return checklist?.items?.length || 0;
  };

  // Métodos
  const loadChecklists = async () => {
    try {
      loading.value = true;
      console.log('Carregando checklists para o card:', props.cardId);

      const response = await axios.get(`/api/cards/${props.cardId}/checklists`);

      let checklistsData = [];
      if (response.data && response.data.data) {
        checklistsData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        checklistsData = response.data;
      }

      // Garante que cada checklist tenha um array de items
      checklistsData.forEach(checklist => {
        if (!checklist.items) {
          checklist.items = [];
        }
        // Ordena os itens por posição
        checklist.items.sort((a, b) => a.position - b.position);
        newItems[checklist.id] = '';
      });

      checklists.value = checklistsData;

      if (checklists.value.length === 0) {
        emit('no-checklists');
      }
    } catch (error) {
      console.error('Erro ao carregar checklists:', error);
      showErrorToast('Erro ao carregar checklists');
    } finally {
      loading.value = false;
    }
  };

  const addItem = async (checklistIndex, checklistId) => {
    const content = newItems[checklistId]?.trim();
    if (!content) return;

    try {
      const response = await axios.post(`/api/checklists/${checklistId}/items`, {
        content: content,
      });

      console.log('Resposta completa do servidor:', response);
      console.log('Dados retornados:', response.data);

      // O backend retorna { item: {...}, checklist: {...} }
      const itemData = response.data.item;

      if (!itemData || !itemData.id) {
        console.error('Item criado sem ID válido:', response.data);
        throw new Error('Item criado sem ID válido');
      }

      const newItem = {
        id: itemData.id,
        content: itemData.content,
        is_completed: itemData.is_completed || false,
        checklist_id: itemData.checklist_id || checklistId,
        position: itemData.position,
        completed_at: itemData.completed_at || null,
      };

      console.log('Novo item processado:', newItem);

      // Adiciona o item usando o índice
      checklists.value[checklistIndex].items.push(newItem);

      // Força Vue a detectar a mudança
      checklists.value = [...checklists.value];

      // Mostra toast de sucesso
      showSuccessToast('Item adicionado com sucesso!');

      // Limpa o campo
      newItems[checklistId] = '';

      // Força atualização visual
      forceUpdate();

      // Emite evento
      await nextTick();
      emitUpdate();
    } catch (error) {
      console.error('Erro ao adicionar item:', error);
      console.error('Resposta do erro:', error.response);
      showErrorToast(error.response?.data?.message || 'Erro ao adicionar item');
    }
  };

  const toggleItem = async (checklistIndex, itemId, isCompleted) => {
    console.log('Toggle item:', { checklistIndex, itemId, isCompleted });

    // Valida se o itemId é válido
    if (!itemId || itemId === undefined || itemId === null) {
      console.error('ID do item inválido:', itemId);
      showErrorToast('ID do item inválido. Recarregue a página.');
      return;
    }

    try {
      // Tenta primeiro com a rota direta
      let response;
      try {
        response = await axios.put(`/api/checklist-items/${itemId}`, {
          is_completed: isCompleted,
        });
      } catch (error) {
        if (error.response?.status === 404) {
          // Se falhar, tenta com a rota alternativa
          const checklistId = checklists.value[checklistIndex].id;
          response = await axios.put(`/api/checklists/${checklistId}/items/${itemId}`, {
            is_completed: isCompleted,
          });
        } else {
          throw error;
        }
      }

      // Encontra o item usando índices
      const itemIndex = checklists.value[checklistIndex].items.findIndex(i => i.id === itemId);
      if (itemIndex !== -1) {
        // Atualiza o item
        checklists.value[checklistIndex].items[itemIndex].is_completed = isCompleted;
        checklists.value[checklistIndex].items[itemIndex].completed_at = isCompleted
          ? new Date().toISOString()
          : null;

        // Força Vue a detectar a mudança
        checklists.value = [...checklists.value];
      }

      // Força atualização visual
      forceUpdate();

      // Emite evento
      await nextTick();
      emitUpdate();
    } catch (error) {
      console.error('Erro ao atualizar item:', error);
      console.error('URL tentada:', `/api/checklist-items/${itemId}`);

      // Reverte o checkbox em caso de erro
      const itemIndex = checklists.value[checklistIndex].items.findIndex(i => i.id === itemId);
      if (itemIndex !== -1) {
        checklists.value[checklistIndex].items[itemIndex].is_completed = !isCompleted;
        checklists.value = [...checklists.value];
      }

      showErrorToast(error.response?.data?.message || 'Erro ao atualizar item');
    }
  };

  const deleteItem = async (checklistIndex, itemId) => {
    try {
      await axios.delete(`/api/checklist-items/${itemId}`);

      // Remove o item usando splice
      const itemIndex = checklists.value[checklistIndex].items.findIndex(i => i.id === itemId);
      if (itemIndex !== -1) {
        checklists.value[checklistIndex].items.splice(itemIndex, 1);

        // Força Vue a detectar a mudança
        checklists.value = [...checklists.value];
      }

      // Mostra toast de sucesso
      showSuccessToast('Item excluído com sucesso!');

      // Força atualização visual
      forceUpdate();

      // Emite evento
      await nextTick();
      emitUpdate();
    } catch (error) {
      console.error('Erro ao excluir item:', error);
      showErrorToast('Erro ao excluir item');
    }
  };

  const toggleDeleteConfirm = checklistId => {
    if (showDeleteConfirm.value === checklistId) {
      showDeleteConfirm.value = null;
    } else {
      showDeleteConfirm.value = checklistId;
    }
  };

  const closeDeleteConfirm = () => {
    showDeleteConfirm.value = null;
  };

  const executeDeleteChecklist = async (checklistIndex, checklistId) => {
    try {
      await axios.delete(`/api/checklists/${checklistId}`);

      // Remove a checklist
      checklists.value.splice(checklistIndex, 1);

      // Força Vue a detectar a mudança
      checklists.value = [...checklists.value];

      // Remove do objeto newItems
      delete newItems[checklistId];

      // Fecha o popover
      closeDeleteConfirm();

      // Mostra toast de sucesso
      showSuccessToast('Checklist excluída com sucesso!');

      if (checklists.value.length === 0) {
        emit('no-checklists');
      }

      // Emite evento
      await nextTick();
      emitUpdate();
    } catch (error) {
      console.error('Erro ao excluir checklist:', error);
      closeDeleteConfirm();
      showErrorToast('Erro ao excluir checklist');
    }
  };

  // Drag and Drop - Funções melhoradas para evitar conflito
  const handleDragStart = (event, checklistId, item, itemIndex) => {
    // Para o evento de se propagar para o modal pai
    event.stopPropagation();

    // Define dados específicos para o item do checklist
    event.dataTransfer.effectAllowed = 'move';

    // Define um tipo MIME customizado para identificar que é um item de checklist
    const checklistItemData = {
      type: 'checklist-item',
      checklistId: checklistId,
      item: item,
      itemIndex: itemIndex,
    };

    event.dataTransfer.setData('application/x-checklist-item', JSON.stringify(checklistItemData));

    // Adiciona uma imagem de drag customizada (opcional)
    const dragImage = document.createElement('div');
    dragImage.className = 'drag-image';
    dragImage.textContent = item.content;
    dragImage.style.position = 'absolute';
    dragImage.style.top = '-1000px';
    document.body.appendChild(dragImage);
    event.dataTransfer.setDragImage(dragImage, 0, 0);
    setTimeout(() => document.body.removeChild(dragImage), 0);

    // Marca que estamos arrastando um item do checklist
    isDraggingItem.value = true;
    draggedItem.value = item;
    draggedFromChecklist.value = checklistId;
    draggedFromIndex.value = itemIndex;

    // Adiciona classe para efeito visual
    event.target.closest('.checklist-item').classList.add('dragging');
  };

  const handleDragEnd = event => {
    event.stopPropagation();

    // Remove a classe de dragging
    const draggingElements = document.querySelectorAll('.dragging');
    draggingElements.forEach(el => el.classList.remove('dragging'));

    // Remove indicador de drop se existir
    if (dropIndicator.value) {
      dropIndicator.value.remove();
      dropIndicator.value = null;
    }

    // Reseta os estados
    isDraggingItem.value = false;
    draggedItem.value = null;
    draggedFromChecklist.value = null;
    draggedFromIndex.value = null;
  };

  const handleChecklistDragOver = event => {
    // Verifica se estamos arrastando um item de checklist
    if (!isDraggingItem.value) {
      // Se não for um item de checklist, deixa o evento bubble para o upload handler
      return;
    }

    // Previne o comportamento padrão apenas para itens de checklist
    event.preventDefault();
    event.stopPropagation();

    event.dataTransfer.dropEffect = 'move';

    // Encontra o item mais próximo para mostrar onde será inserido
    const afterElement = getDragAfterElement(event.currentTarget, event.clientY);

    // Remove indicador anterior se existir
    if (dropIndicator.value) {
      dropIndicator.value.remove();
    }

    // Cria indicador visual de onde o item será inserido
    const indicator = document.createElement('div');
    indicator.className = 'drop-indicator';
    indicator.style.height = '2px';
    indicator.style.backgroundColor = '#007bff';
    indicator.style.margin = '4px 0';

    if (afterElement == null) {
      event.currentTarget.appendChild(indicator);
    } else {
      event.currentTarget.insertBefore(indicator, afterElement);
    }

    dropIndicator.value = indicator;
  };

  const handleDragLeave = event => {
    // Remove o indicador se sair da área
    if (event.target === event.currentTarget && dropIndicator.value) {
      dropIndicator.value.remove();
      dropIndicator.value = null;
    }
  };

  const handleChecklistDrop = async (event, targetChecklistId) => {
    // Verifica se é um item de checklist
    let checklistItemData;
    try {
      checklistItemData = event.dataTransfer.getData('application/x-checklist-item');
      if (!checklistItemData) {
        // Não é um item de checklist, deixa o evento bubble para o upload
        return;
      }

      checklistItemData = JSON.parse(checklistItemData);
      if (checklistItemData.type !== 'checklist-item') {
        // Não é um item de checklist
        return;
      }
    } catch (e) {
      // Não é um JSON válido, provavelmente é um arquivo
      return;
    }

    // É um item de checklist, processa o drop
    event.preventDefault();
    event.stopPropagation();

    // Remove o indicador
    if (dropIndicator.value) {
      dropIndicator.value.remove();
      dropIndicator.value = null;
    }

    const { checklistId: sourceChecklistId, item, itemIndex: sourceIndex } = checklistItemData;

    // Encontra a posição onde o item foi solto
    const dropPosition = getDropPosition(event.currentTarget, event.clientY);

    try {
      // Encontra os índices das checklists
      const sourceChecklistIndex = checklists.value.findIndex(c => c.id === sourceChecklistId);
      const targetChecklistIndex = checklists.value.findIndex(c => c.id === targetChecklistId);

      if (sourceChecklistIndex === -1 || targetChecklistIndex === -1) return;

      // Remove o item da posição original
      checklists.value[sourceChecklistIndex].items.splice(sourceIndex, 1);

      // Adiciona na nova posição
      checklists.value[targetChecklistIndex].items.splice(dropPosition, 0, item);

      // Se mudou de checklist, atualiza o checklist_id
      if (sourceChecklistId !== targetChecklistId) {
        item.checklist_id = targetChecklistId;

        // Atualiza no backend
        await axios.put(`/api/checklist-items/${item.id}`, {
          checklist_id: targetChecklistId,
          position: dropPosition,
        });
      }

      // Atualiza as posições de todos os itens afetados
      await updateItemPositions(targetChecklistId);

      if (sourceChecklistId !== targetChecklistId) {
        await updateItemPositions(sourceChecklistId);
      }

      // Força atualização visual
      checklists.value = [...checklists.value];
      forceUpdate();
      emitUpdate();

      showSuccessToast('Item reordenado com sucesso!');
    } catch (error) {
      console.error('Erro no drag and drop:', error);
      showErrorToast('Erro ao reordenar itens');
      // Recarrega os checklists em caso de erro
      loadChecklists();
    }
  };

  const getDragAfterElement = (container, y) => {
    const draggableElements = [...container.querySelectorAll('.checklist-item:not(.dragging)')];

    return draggableElements.reduce(
      (closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        } else {
          return closest;
        }
      },
      { offset: Number.NEGATIVE_INFINITY }
    ).element;
  };

  const getDropPosition = (container, y) => {
    const items = [...container.querySelectorAll('.checklist-item:not(.dragging)')];

    for (let i = 0; i < items.length; i++) {
      const box = items[i].getBoundingClientRect();
      const offset = y - box.top - box.height / 2;

      if (offset < 0) {
        return i;
      }
    }

    return items.length;
  };

  const updateItemPositions = async checklistId => {
    const checklistIndex = checklists.value.findIndex(c => c.id === checklistId);
    if (checklistIndex === -1) return;

    const items = checklists.value[checklistIndex].items;

    // Atualiza as posições na memória
    items.forEach((item, index) => {
      item.position = index;
    });

    try {
      // Envia as novas posições para o backend
      await axios.put(`/api/checklists/${checklistId}/reorder-items`, {
        items: items.map((item, index) => ({
          id: item.id,
          position: index,
        })),
      });
    } catch (error) {
      console.error('Erro ao atualizar posições:', error);
      // Se a rota de reordenar não existir, atualiza item por item
      if (error.response?.status === 404) {
        for (let i = 0; i < items.length; i++) {
          try {
            await axios.put(`/api/checklist-items/${items[i].id}`, {
              position: i,
            });
          } catch (err) {
            console.error(`Erro ao atualizar posição do item ${items[i].id}:`, err);
          }
        }
      }
    }
  };

  // Funções de edição do título do checklist
  const startEditChecklistTitle = checklist => {
    editingChecklistId.value = checklist.id;
    editingChecklistTitle.value = checklist.title;

    nextTick(() => {
      if (checklistTitleInput.value) {
        checklistTitleInput.value.focus();
        checklistTitleInput.value.select();
      }
    });
  };

  const saveChecklistTitle = async checklist => {
    const newTitle = editingChecklistTitle.value.trim();

    if (!newTitle) {
      cancelEditChecklistTitle();
      return;
    }

    if (newTitle === checklist.title) {
      cancelEditChecklistTitle();
      return;
    }

    try {
      await axios.put(`/api/checklists/${checklist.id}`, {
        title: newTitle,
      });

      // Atualiza o título localmente
      const index = checklists.value.findIndex(c => c.id === checklist.id);
      if (index !== -1) {
        checklists.value[index].title = newTitle;
      }

      showSuccessToast('Título atualizado com sucesso!');
      cancelEditChecklistTitle();
      emitUpdate();
    } catch (error) {
      console.error('Erro ao atualizar título:', error);
      showErrorToast('Erro ao atualizar título');
      cancelEditChecklistTitle();
    }
  };

  const cancelEditChecklistTitle = () => {
    editingChecklistId.value = null;
    editingChecklistTitle.value = '';
  };

  // Funções de edição do conteúdo do item
  const startEditItemContent = item => {
    // Não permite editar se o item estiver marcado como completo
    if (item.is_completed) {
      showWarningToast('Desmarque o item antes de editar');
      return;
    }

    editingItemId.value = item.id;
    editingItemContent.value = item.content;

    nextTick(() => {
      if (itemContentInput.value) {
        itemContentInput.value.focus();
        itemContentInput.value.select();
      }
    });
  };

  const saveItemContent = async (checklistId, item) => {
    const newContent = editingItemContent.value.trim();

    if (!newContent) {
      cancelEditItemContent();
      return;
    }

    if (newContent === item.content) {
      cancelEditItemContent();
      return;
    }

    try {
      await axios.put(`/api/checklist-items/${item.id}`, {
        content: newContent,
      });

      // Atualiza o conteúdo localmente
      const checklistIndex = checklists.value.findIndex(c => c.id === checklistId);
      if (checklistIndex !== -1) {
        const itemIndex = checklists.value[checklistIndex].items.findIndex(i => i.id === item.id);
        if (itemIndex !== -1) {
          checklists.value[checklistIndex].items[itemIndex].content = newContent;
        }
      }

      showSuccessToast('Item atualizado com sucesso!');
      cancelEditItemContent();
      emitUpdate();
    } catch (error) {
      console.error('Erro ao atualizar item:', error);
      showErrorToast('Erro ao atualizar item');
      cancelEditItemContent();
    }
  };

  const cancelEditItemContent = () => {
    editingItemId.value = null;
    editingItemContent.value = '';
  };

  // Emite atualização com dados completos
  const emitUpdate = () => {
    const checklistsData = checklists.value.map((checklist, index) => ({
      ...checklist,
      progress: getProgress(index),
      completed_count: getCompletedCount(index),
      total_count: getTotalCount(index),
    }));

    console.log('Emitindo atualização:', checklistsData);
    emit('updated', { checklists: checklistsData });
  };

  // Watchers
  watch(
    () => props.cardId,
    newVal => {
      if (newVal) {
        loadChecklists();
      }
    },
    { immediate: true }
  );

  // Lifecycle
  onMounted(() => {
    if (props.cardId) {
      loadChecklists();
    } else {
      console.error('CardId não fornecido ao ChecklistPanel');
      loading.value = false;
    }

    // Fecha o popover ao clicar fora
    document.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
  });

  // Fecha o popover ao clicar fora
  const handleClickOutside = event => {
    // Fecha popover de exclusão
    if (
      !event.target.closest('.delete-confirm-popover') &&
      !event.target.closest('.btn-link.text-danger')
    ) {
      closeDeleteConfirm();
    }

    // Salva edição de checklist se clicar fora
    if (
      editingChecklistId.value &&
      !event.target.closest('.checklist-title') &&
      !event.target.closest('input')
    ) {
      const checklist = checklists.value.find(c => c.id === editingChecklistId.value);
      if (checklist) {
        saveChecklistTitle(checklist);
      }
    }

    // Salva edição de item se clicar fora
    if (
      editingItemId.value &&
      !event.target.closest('.checklist-item') &&
      !event.target.closest('input')
    ) {
      const checklistWithItem = checklists.value.find(c =>
        c.items.some(i => i.id === editingItemId.value)
      );
      if (checklistWithItem) {
        const item = checklistWithItem.items.find(i => i.id === editingItemId.value);
        if (item) {
          saveItemContent(checklistWithItem.id, item);
        }
      }
    }
  };
</script>

<style scoped>
  /* Estilos para títulos editáveis */
  .editable-title {
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
  }

  .editable-title:hover {
    background-color: #f0f0f0;
    text-decoration: underline dotted;
    text-underline-offset: 3px;
  }

  .editable-title:hover::after {
    content: ' ✏️';
    font-size: 0.8em;
    opacity: 0.7;
    margin-left: 4px;
  }

  /* Estilos para itens editáveis */
  .editable-item-content {
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
  }

  .editable-item-content:hover {
    background-color: #f0f0f0;
    text-decoration: underline dotted;
    text-underline-offset: 3px;
  }

  .editable-item-content:hover::after {
    content: ' ✏️';
    font-size: 0.8em;
    opacity: 0.7;
    margin-left: 4px;
  }

  /* Não mostra hover em itens completados */
  .editable-item-content.text-decoration-line-through:hover {
    background-color: transparent;
    text-decoration: line-through !important;
  }

  .editable-item-content.text-decoration-line-through:hover::after {
    content: '';
  }

  /* Inputs de edição inline */
  .checklist-title input.form-control-sm,
  .checklist-item input.form-control-sm {
    height: auto;
    padding: 2px 8px;
    font-size: inherit;
    font-weight: inherit;
    line-height: 1.2;
  }

  .checklist-title input.form-control-sm {
    font-weight: 600;
  }

  .checklist-item {
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }

  .checklist-item:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
  }

  .checklist-item.dragging {
    opacity: 0.5;
    background-color: #e9ecef;
  }

  .drag-handle {
    cursor: move;
    opacity: 0.4;
    transition: opacity 0.2s;
  }

  .checklist-item:hover .drag-handle {
    opacity: 1;
  }

  .drop-indicator {
    transition: all 0.2s ease;
  }

  .delete-confirm-popover {
    position: absolute;
    right: 0;
    top: 100%;
    z-index: 1050;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    padding: 1rem;
    min-width: 300px;
    margin-top: 0.5rem;
  }

  .delete-confirm-popover .arrow {
    position: absolute;
    top: -8px;
    right: 20px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid white;
  }

  .delete-confirm-popover .arrow::before {
    content: '';
    position: absolute;
    top: -1px;
    left: -8px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid #dee2e6;
  }

  /* Estilo para imagem de drag customizada */
  .drag-image {
    background: white;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Indicador visual durante o drag */
  .checklist-items {
    min-height: 40px;
    position: relative;
  }

  .checklist-items.drag-over {
    background-color: #f0f8ff;
    border: 2px dashed #007bff;
  }
</style>
