<template>
  <div class="container-fluid">
    <h4 class="fw-bold py-0 mb-4">
      Tarefas /
      <span class="text-muted fw-light">{{ board.title }}</span>
    </h4>

    <!-- Formulário para adicionar nova coluna -->
    <div class="d-flex ml-3 flex-column" style="min-width: 300px; float: right">
      <div v-if="addingColumn" class="w-100X">
        <input
          v-model="newColumnName"
          class="form-control mb-2"
          placeholder="Nome da nova coluna"
        />
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-primary" @click="createColumn">Salvar</button>
          <button class="btn btn-sm btn-secondary" @click="addingColumn = false">Cancelar</button>
        </div>
      </div>
      <button v-else class="btn btn-outline-secondary" @click="addingColumn = true">
        + Adicionar Nova Coluna
      </button>
    </div>
    <!-- FIM Formulário para adicionar nova coluna -->

    <!-- Debug info -->
    <div v-if="loading" class="alert alert-info">Carregando dados...</div>
    <div v-if="error" class="alert alert-danger">Erro ao carregar dados: {{ error }}</div>

    <!-- Botão para depuração - Carrega as colunas manualmente -->
    <div v-if="board.columns && board.columns.length === 0" class="alert alert-warning">
      Nenhuma coluna encontrada para este board.
      <button @click="loadColumns" class="btn btn-sm btn-primary ms-2">
        Carregar colunas manualmente
      </button>
      <pre class="mt-2 bg-light p-2">{{ JSON.stringify(board, null, 2) }}</pre>
    </div>

    <!-- Colunas -->
    <main class="kanban-drag">
      <draggable
        v-model="board.columns"
        group="columns"
        item-key="id"
        class="app-kanban d-flex gap-3 overflow-auto"
        @end="onColumnDragEnd"
      >
        <template #item="{ element: column }">
          <div
            class="kanban-board d-flex flex-column rounded"
            :style="{
              '--col-color': column.color || '#cfcfcf',
              width: '300px',
              maxHeight: 'calc(100vh - 170px)',
            }"
          >
            <!-- Cabeçalho da coluna -->
            <header
              class="kanban-title d-flex justify-between items-center p-2 rounded relative text-white text-2"
            >
              <span v-if="editingColumnId !== column.id" @click="startEditColumn(column)">
                {{ column.name }}
              </span>
              <input
                v-else
                class="form-control form-control-sm"
                v-model="editedColumnName"
                @blur="saveColumnName(column)"
                @keyup.enter="saveColumnName(column)"
                @keyup.esc="cancelEditColumn"
              />
              <ColumnMenu :column="column" @update:column="updateColumn" />
            </header>

            <!-- Cards com scroll vertical -->
            <main class="kanban-drag p-2 overflow-auto flex-grow-1 custom-scroll">
              <draggable
                v-model="column.cards"
                group="cards"
                item-key="id"
                class="kanban-drag-inner d-flex flex-column gap-2"
                @end="onDragEnd"
              >
                <template #item="{ element: card }">
                  <div
                    v-if="![1, 5].includes(card.status)"
                    class="kanban-card kanban-item p-2 position-relative bg-white border rounded"
                    :data-created-at="card.created_at"
                    @click="openCardModal(card.id)"
                  >
                    <span
                      class="badge position-absolute top-0 end-0 m-1"
                      :class="`bg-${card.color || 'secondary'}`"
                    >
                      &nbsp;
                    </span>
                    <h6>{{ card.title }}</h6>
                    <!-- <small class="small text-muted">{{ card.description }}</small> -->
                    <div v-if="card.due_date" class="mt-1 d-flex align-items-center gap-1 small">
                      <i class="fa-regular fa-calendar text-muted"></i>
                      <span
                        :class="[
                          'badge rounded-pill px-2',
                          isOverdue(card.due_date)
                            ? 'bg-danger text-white'
                            : 'bg-success text-white',
                        ]"
                      >
                        {{ formatDate(card.due_date) }}
                      </span>
                    </div>

                    <div class="d-flex align-items-center gap-1 mt-2 icons-cards small text-muted">
                      <i
                        v-if="card.full_description"
                        class="fa-solid fa-align-left mr-3"
                        title="Descrição completa"
                      ></i>
                      <template v-if="card.attachments?.length > 0">
                        <i class="fa-solid fa-paperclip" title="Anexos"></i>
                        {{ card.attachments.length }}
                      </template>
                      <template v-if="card.comments_count > 0">
                        <i class="fa-regular fa-comment-dots ml-3" title="Comentários"></i>
                        {{ card.comments_count }}
                      </template>
                    </div>
                  </div>
                </template>
              </draggable>
              <!-- botão adicionar -->
              <div v-if="addingCardTo !== column.id" class="text-center mt-3 mb-3">
                <button class="btn btn-sm btn-label-secondary" @click="showAddCardForm(column.id)">
                  + Adicionar novo cartão
                </button>
              </div>
              <!-- formulário novo card -->
              <div v-else class="bg-white border rounded p-2 mt-2">
                <input
                  v-model="newCard.title"
                  class="form-control mb-1"
                  placeholder="Título"
                  required
                />
                <textarea
                  v-model="newCard.description"
                  class="form-control mb-2"
                  placeholder="Descrição (opcional)"
                ></textarea>
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn btn-sm btn-primary" @click="saveNewCard(column.id)">
                    Salvar
                  </button>
                  <button class="btn btn-sm btn-secondary" @click="cancelAddCard">Cancelar</button>
                </div>
              </div>
              <!-- fim formulário novo card -->
            </main>
          </div>
        </template>
      </draggable>
    </main>
    <!-- FIM Colunas -->

    <!-- Modal -->
    <CardModal
      v-if="showModal"
      :card-id="selectedCardId"
      @close="showModal = false"
      @update-card="updateCard"
      @remove-card="removeCardFromBoard"
    />
  </div>
</template>

<script setup>
  import { onMounted, ref, nextTick } from 'vue';
  import axios from 'axios';
  import draggable from 'vuedraggable';
  import CardModal from './CardModal.vue';
  import ColumnMenu from './ColumnMenu.vue';

  const props = defineProps({
    boardId: {
      type: Number,
      required: true,
    },
  });

  const board = ref({ title: '', columns: [] });
  const selectedCardId = ref(null);
  const showModal = ref(false);
  const loading = ref(false);
  const error = ref(null);

  onMounted(async () => {
    await loadBoard();
    nextTick(() => {
      // Aplica o Perfect Scrollbar em todas as colunas
      const columns = document.querySelectorAll('.kanban-drag');
      columns.forEach(col => {
        const ps = new PerfectScrollbar(col);
        psInstances.push(ps);
      });
    });
  });

  const loadBoard = async () => {
    loading.value = true;
    error.value = null;

    try {
      await axios.get('/sanctum/csrf-cookie');
      const { data } = await axios.get(`/api/board/${props.boardId}`);
      board.value = data;
      if (!board.value.columns) {
        await loadColumns();
      }
    } catch (err) {
      error.value = err.message || 'Erro desconhecido ao carregar o board';
    } finally {
      loading.value = false;
    }
  };

  const loadColumns = async () => {
    try {
      loading.value = true;
      const { data } = await axios.get(`/api/board/${props.boardId}/columns`);
      if (Array.isArray(data)) {
        board.value.columns = data;
      } else if (data && data.columns && Array.isArray(data.columns)) {
        board.value.columns = data.columns;
      }
    } catch (err) {
      error.value = 'Erro ao carregar as colunas do quadro';
    } finally {
      loading.value = false;
    }
  };

  const onDragEnd = async () => {
    try {
      for (const col of board.value.columns) {
        const cardIds = col.cards.map(c => c.id);
        await axios.post('/api/cards/update-order', {
          cards: cardIds,
          column_id: col.id,
        });
      }
    } catch (err) {
      console.error('Erro ao atualizar ordem dos cards:', err);
      alert('Erro ao salvar a nova ordem dos cards');
    }
  };

  const onColumnDragEnd = async () => {
    try {
      const payload = board.value.columns.map((col, index) => ({
        id: col.id,
        order: index,
      }));
      await axios.post('/api/columns/update-order', { columns: payload });
    } catch (err) {
      console.error('Erro ao atualizar a ordem das colunas:', err);
      alert('Erro ao salvar a nova ordem das colunas');
    }
  };

  function openCardModal(cardId) {
    selectedCardId.value = cardId;
    showModal.value = true;
  }

  function updateCard(updatedCard) {
    for (const column of board.value.columns) {
      const index = column.cards.findIndex(c => c.id === updatedCard.id);
      if (index !== -1) {
        // Substitui o objeto antigo por um novo objeto
        column.cards.splice(index, 1, { ...updatedCard });
        break;
      }
    }
  }

  const removeCardFromBoard = cardId => {
    for (const column of board.value.columns) {
      const index = column.cards.findIndex(c => c.id === cardId);
      if (index !== -1) {
        column.cards.splice(index, 1);
        break;
      }
    }
  };

  /* Menu coluna  ************************************************************************/
  function updateColumn(updatedCol) {
    const idx = board.value.columns.findIndex(c => c.id === updatedCol.id);
    if (idx !== -1) board.value.columns[idx] = updatedCol;
  }
  /* FIM Menu coluna */

  /* Funções para editar o nome da coluna */
  const editingColumnId = ref(null);
  const editedColumnName = ref('');

  function startEditColumn(column) {
    editingColumnId.value = column.id;
    editedColumnName.value = column.name;
  }

  function cancelEditColumn() {
    editingColumnId.value = null;
    editedColumnName.value = '';
  }

  async function saveColumnName(column) {
    const newName = editedColumnName.value.trim();
    if (!newName || newName === column.name) {
      cancelEditColumn();
      return;
    }

    try {
      await axios.put(`/api/columns/${column.id}`, { name: newName });
      column.name = newName;
      cancelEditColumn();
    } catch (err) {
      console.error('Erro ao renomear coluna:', err);
    }
  }
  /* fim Funções para editar nome da coluna ********************************************************/

  /* Funções para adicionar novo cartão  *********************************************************/
  const addingCardTo = ref(null);
  const newCard = ref({ title: '', description: '' });

  function showAddCardForm(columnId) {
    addingCardTo.value = columnId;
    newCard.value = { title: '', description: '' };
  }

  function cancelAddCard() {
    addingCardTo.value = null;
    newCard.value = { title: '', description: '' };
  }

  async function saveNewCard(columnId) {
    if (!newCard.value.title.trim()) return;

    try {
      const res = await axios.post('/api/cards', {
        column_id: columnId,
        title: newCard.value.title,
        description: newCard.value.description,
      });

      const defaultCard = {
        full_description: '',
        link: '',
        color: 'secondary',
        attachments: [],
      };

      const column = board.value.columns.find(c => c.id === columnId);
      if (column) {
        column.cards.push({ ...defaultCard, ...res.data.card });
      }

      cancelAddCard();
    } catch (err) {
      console.error('Erro ao adicionar novo card:', err);
    }
  }

  function save() {
    emit('update:card', { ...localCard.value });
    emit('close');
  }
  /* fim Funções para adicionar novo cartão *****************************************************/

  /* Adicionar nova COLUNA **********************************************************************/
  const addingColumn = ref(false);
  const newColumnName = ref('');

  async function createColumn() {
    if (!newColumnName.value.trim()) return;

    try {
      const res = await axios.post('/api/columns', {
        board_id: props.boardId,
        name: newColumnName.value,
      });

      const newCol = {
        ...res.data.column,
        cards: [],
      };

      board.value.columns.push(newCol);
      newColumnName.value = '';
      addingColumn.value = false;
    } catch (err) {
      console.error('Erro ao criar coluna:', err);
    }
  }
  /* Fim Adicionar nova COLUNA ******************************************************************/

  /* Função para verificar se a data está atrasada *********************************************/
  const isOverdue = dateStr => {
    if (!dateStr) return false;
    const now = new Date();
    const date = new Date(dateStr);
    return date < now;
  };
  const formatDate = datetime => {
    if (!datetime) return '';
    const date = new Date(datetime);
    return date.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    });
  };

  /* fim Função para verificar se a data está atrasada *****************************************/
</script>

<style scoped>
  /* Scroll moderno e fino */
  .custom-scroll::-webkit-scrollbar {
    width: 5px;
    height: 5px;
  }

  .custom-scroll::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0);
    border-radius: 4px;
  }
  .custom-scroll:hover::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.4); /* cinza claro */
  }
  /* Css Data Atrasada */
  .badge.bg-success {
    background-color: #28a745 !important;
  }

  .badge.bg-danger {
    background-color: #dc3545 !important;
  }
</style>
