<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold py-0 mb-0">
        Tarefas /
        <span class="text-muted fw-light">Meus Quadros</span>
      </h4>
      <p class="text-muted mb-0">
        {{ filteredBoards.length }} quadro{{ filteredBoards.length !== 1 ? 's' : '' }} encontrado{{
          filteredBoards.length !== 1 ? 's' : ''
        }}
      </p>
    </div>

    <div class="d-flex gap-2">
      <!-- Ordenação -->
      <div class="dropdown">
        <button
          class="btn btn-outline-secondary dropdown-toggle"
          type="button"
          data-bs-toggle="dropdown"
        >
          <i class="bx bx-sort me-1"></i>
          {{ sortOptions[currentSort].label }}
        </button>
        <ul class="dropdown-menu">
          <li v-for="(option, key) in sortOptions" :key="key">
            <a
              class="dropdown-item"
              :class="{ active: currentSort === key }"
              @click="currentSort = key"
              href="#"
            >
              {{ option.label }}
            </a>
          </li>
        </ul>
      </div>

      <!-- Botão Criar Quadro -->
      <button class="btn btn-primary" @click="showCreateModal = true">
        <i class="bx bx-plus me-1"></i>
        Criar Quadro
      </button>
    </div>
  </div>

  <!-- Barra de Pesquisa -->
  <div class="row mb-4">
    <div class="col-12 col-md-6">
      <div class="input-group">
        <span class="input-group-text">
          <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input
          v-model="searchQuery"
          type="text"
          class="form-control"
          placeholder="Pesquisar quadros..."
        />
      </div>
    </div>
  </div>

  <!-- Loading State -->
  <div v-if="loading" class="text-center py-5">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Carregando...</span>
    </div>
  </div>

  <!-- Lista de Boards -->
  <div v-else class="row">
    <transition-group name="board-list" tag="div" class="row">
      <div v-for="board in filteredBoards" :key="board.id" class="col-md-4 mb-4">
        <div
          class="card h-100 board-card"
          :style="{ borderTop: `4px solid ${board.color || '#6366F1'}` }"
        >
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title mb-0">{{ board.title }}</h5>

              <!-- Dropdown Menu -->
              <div class="dropdown">
                <button
                  class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                  type="button"
                  data-bs-toggle="dropdown"
                >
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" @click="editBoard(board)" href="#">
                      <i class="bx bx-edit me-2"></i>
                      Editar
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" @click="archiveBoard(board)" href="#">
                      <i class="bx bx-archive me-2"></i>
                      Arquivar
                    </a>
                  </li>
                  <li><hr class="dropdown-divider" /></li>
                  <li>
                    <a class="dropdown-item text-danger" @click="deleteBoard(board)" href="#">
                      <i class="bx bx-trash me-2"></i>
                      Excluir
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <p class="text-muted small mb-3">
              <i class="bx bx-calendar me-1"></i>
              Criado em {{ formatDate(board.created_at) }}
              <br />
              <i class="bx bx-user me-1"></i>
              Por {{ board.user?.name || 'Usuário' }}
            </p>

            <a
              :href="`/board/${board.uuid || board.id}`"
              @click="updateLastViewed(board.id)"
              class="stretched-link"
            ></a>
          </div>
        </div>
      </div>
    </transition-group>
  </div>

  <!-- Empty State -->
  <div v-if="!loading && filteredBoards.length === 0" class="text-center py-5">
    <i class="bx bx-folder-open display-1 text-muted mb-3"></i>
    <p class="text-muted">
      {{
        searchQuery
          ? 'Nenhum quadro encontrado com sua pesquisa.'
          : 'Você ainda não tem nenhum quadro.'
      }}
    </p>
    <button v-if="!searchQuery" class="btn btn-primary" @click="showCreateModal = true">
      <i class="bx bx-plus me-1"></i>
      Criar Primeiro Quadro
    </button>
  </div>

  <!-- Modal Criar/Editar Quadro -->
  <div
    class="modal fade"
    :class="{ show: showCreateModal }"
    :style="{ display: showCreateModal ? 'block' : 'none' }"
    tabindex="-1"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ editingBoard ? 'Editar' : 'Novo' }} Quadro</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveBoard">
            <div class="mb-3">
              <label for="boardTitle" class="form-label">Nome do Quadro</label>
              <input
                v-model="boardForm.title"
                type="text"
                class="form-control"
                id="boardTitle"
                placeholder="Digite o nome do quadro"
                required
                ref="titleInput"
              />
            </div>

            <div class="mb-3">
              <label class="form-label">Selecione uma Cor</label>
              <div class="color-picker-grid">
                <div
                  v-for="color in colorOptions"
                  :key="color"
                  class="color-option"
                  :class="{ selected: boardForm.color === color }"
                  :style="{ backgroundColor: color }"
                  @click="boardForm.color = color"
                >
                  <i v-if="boardForm.color === color" class="bx bx-check"></i>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
          <button type="button" class="btn btn-primary" @click="saveBoard" :disabled="savingBoard">
            <span v-if="savingBoard" class="spinner-border spinner-border-sm me-2"></span>
            {{ editingBoard ? 'Salvar Alterações' : 'Criar Quadro' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Backdrop -->
  <div v-if="showCreateModal" class="modal-backdrop fade show" @click="closeModal"></div>

  <!-- Modal Confirmação -->
  <div
    class="modal fade"
    :class="{ show: showConfirmModal }"
    :style="{ display: showConfirmModal ? 'block' : 'none' }"
    tabindex="-1"
  >
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ confirmAction.title }}</h5>
          <button type="button" class="btn-close" @click="showConfirmModal = false"></button>
        </div>
        <div class="modal-body">
          <p>{{ confirmAction.message }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="showConfirmModal = false">
            Cancelar
          </button>
          <button
            type="button"
            :class="`btn ${confirmAction.type === 'delete' ? 'btn-danger' : 'btn-warning'}`"
            @click="confirmActionHandler"
          >
            {{ confirmAction.buttonText }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Backdrop Confirmação -->
  <div v-if="showConfirmModal" class="modal-backdrop fade show"></div>

  <!-- Toast Notifications -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div v-if="toast.show" class="toast show" :class="`border-${toast.type}`" role="alert">
      <div class="toast-header">
        <i :class="`bx ${toast.icon} me-2 text-${toast.type}`"></i>
        <strong class="me-auto">{{ toast.title }}</strong>
        <button type="button" class="btn-close" @click="toast.show = false"></button>
      </div>
      <div class="toast-body">
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, nextTick } from 'vue';
  import axios from 'axios';

  // Setup CSRF token
  axios.defaults.headers.common['X-CSRF-TOKEN'] = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

  // Estado
  const boards = ref([]);
  const loading = ref(true);
  const searchQuery = ref('');
  const currentSort = ref('recent');
  const showCreateModal = ref(false);
  const showConfirmModal = ref(false);
  const savingBoard = ref(false);
  const editingBoard = ref(null);
  const titleInput = ref(null);

  // Form Data
  const boardForm = ref({
    title: '',
    color: '#6366F1',
  });

  // Confirmação
  const confirmAction = ref({
    type: '',
    title: '',
    message: '',
    buttonText: '',
    board: null,
  });

  // Toast
  const toast = ref({
    show: false,
    type: 'success',
    title: '',
    message: '',
    icon: 'bx-check-circle',
  });

  // Opções
  const sortOptions = {
    recent: { label: 'Visualizado Recentemente', field: 'last_viewed_at' },
    created: { label: 'Data de Criação', field: 'created_at' },
    name: { label: 'Nome (A-Z)', field: 'title' },
  };

  const colorOptions = [
    '#FF6B6B',
    '#4ECDC4',
    '#45B7D1',
    '#96CEB4',
    '#FECA57',
    '#FF9FF3',
    '#6366F1',
    '#8B5CF6',
    '#EC4899',
    '#14B8A6',
    '#10B981',
    '#F59E0B',
    '#EF4444',
    '#3B82F6',
    '#6B7280',
    '#F97316',
    '#84CC16',
    '#06B6D4',
    '#D946EF',
    '#1F2937',
  ];

  // Computed
  const filteredBoards = computed(() => {
    let filtered = boards.value.filter(board => board.status === 0);

    // Filtrar por pesquisa
    if (searchQuery.value) {
      filtered = filtered.filter(board =>
        board.title.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    }

    // Ordenar
    const sortField = sortOptions[currentSort.value].field;
    filtered.sort((a, b) => {
      if (sortField === 'title') {
        return a.title.localeCompare(b.title);
      }
      return new Date(b[sortField]) - new Date(a[sortField]);
    });

    return filtered;
  });

  // Métodos
  const fetchBoards = async () => {
    try {
      const response = await axios.get('/api/boards');
      boards.value = response.data;
    } catch (error) {
      console.error('Erro ao buscar boards:', error);
      showToast('error', 'Erro', 'Não foi possível carregar os quadros.');
    } finally {
      loading.value = false;
    }
  };

  const saveBoard = async () => {
    if (!boardForm.value.title.trim()) return;

    savingBoard.value = true;

    try {
      if (editingBoard.value) {
        // Atualizar
        const response = await axios.put(`/api/boards/${editingBoard.value.id}`, boardForm.value);
        const index = boards.value.findIndex(b => b.id === editingBoard.value.id);
        boards.value[index] = response.data;
        showToast('success', 'Sucesso', 'Quadro atualizado com sucesso!');
      } else {
        // Criar
        const response = await axios.post('/api/boards', boardForm.value);
        boards.value.unshift(response.data);
        showToast('success', 'Sucesso', 'Quadro criado com sucesso!');
      }

      closeModal();
    } catch (error) {
      console.error('Erro ao salvar board:', error);
      showToast('error', 'Erro', 'Não foi possível salvar o quadro.');
    } finally {
      savingBoard.value = false;
    }
  };

  const editBoard = board => {
    editingBoard.value = board;
    boardForm.value = {
      title: board.title,
      color: board.color,
    };
    showCreateModal.value = true;
    nextTick(() => {
      titleInput.value?.focus();
    });
  };

  const archiveBoard = board => {
    confirmAction.value = {
      type: 'archive',
      title: 'Arquivar Quadro',
      message: `Deseja arquivar o quadro "${board.title}"?`,
      buttonText: 'Arquivar',
      board: board,
    };
    showConfirmModal.value = true;
  };

  const deleteBoard = board => {
    confirmAction.value = {
      type: 'delete',
      title: 'Excluir Quadro',
      message: `Tem certeza que deseja excluir o quadro "${board.title}"? Esta ação não pode ser desfeita.`,
      buttonText: 'Excluir',
      board: board,
    };
    showConfirmModal.value = true;
  };

  const confirmActionHandler = async () => {
    const { type, board } = confirmAction.value;

    try {
      if (type === 'archive') {
        await axios.patch(`/api/boards/${board.id}/archive`);
        board.status = 1;
        showToast('success', 'Sucesso', 'Quadro arquivado com sucesso!');
      } else if (type === 'delete') {
        await axios.delete(`/api/boards/${board.id}`);
        board.status = 5;
        showToast('success', 'Sucesso', 'Quadro excluído com sucesso!');
      }

      showConfirmModal.value = false;
    } catch (error) {
      console.error(`Erro ao ${type} board:`, error);
      showToast(
        'error',
        'Erro',
        `Não foi possível ${type === 'archive' ? 'arquivar' : 'excluir'} o quadro.`
      );
    }
  };

  const updateLastViewed = async boardId => {
    try {
      await axios.patch(`/api/boards/${boardId}/view`);
    } catch (error) {
      console.error('Erro ao atualizar última visualização:', error);
    }
  };

  const closeModal = () => {
    showCreateModal.value = false;
    editingBoard.value = null;
    boardForm.value = {
      title: '',
      color: '#6366F1',
    };
  };

  const showToast = (type, title, message) => {
    const icons = {
      success: 'bx-check-circle',
      error: 'bx-error-circle',
      warning: 'bx-error',
    };

    toast.value = {
      show: true,
      type,
      title,
      message,
      icon: icons[type],
    };

    setTimeout(() => {
      toast.value.show = false;
    }, 3000);
  };

  const formatDate = date => {
    return new Date(date).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  // Lifecycle
  onMounted(() => {
    fetchBoards();
  });
</script>

<style scoped>
  /* Animações */
  .board-list-enter-active,
  .board-list-leave-active {
    transition: all 0.3s ease;
  }

  .board-list-enter-from {
    opacity: 0;
    transform: translateY(30px);
  }

  .board-list-leave-to {
    opacity: 0;
    transform: translateX(-30px);
  }

  /* Cards */
  .board-card {
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .board-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  }

  .board-card .dropdown {
    position: relative;
    z-index: 10;
  }

  /* Color Picker */
  .color-picker-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 8px;
  }

  .color-option {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: 2px solid transparent;
  }

  .color-option:hover {
    transform: scale(1.1);
  }

  .color-option.selected {
    border-color: #333;
    transform: scale(1.1);
  }

  .color-option i {
    color: white;
    font-size: 20px;
  }

  /* Modal animations */
  .modal.show {
    display: block !important;
    animation: modalFadeIn 0.3s ease;
  }

  .modal-backdrop.show {
    animation: backdropFadeIn 0.3s ease;
  }

  @keyframes modalFadeIn {
    from {
      opacity: 0;
      transform: translateY(-50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes backdropFadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 0.5;
    }
  }

  /* Toast */
  .toast {
    min-width: 300px;
    animation: slideInRight 0.3s ease;
  }

  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
</style>
