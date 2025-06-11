<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <!-- Título à esquerda -->
    <div>
      <h4 class="fw-bold py-0 mb-0">
        <a href="../tarefas-vue">Quadro</a>
        |
        <span class="text-muted fw-light">{{ board.title }}</span>
      </h4>
    </div>

    <div
      class="navbar-nav-right d-flex align-items-center justify-content-end"
      id="navbar-collapse"
    >
      <ul class="navbar-nav flex-row align-items-center ms-md-auto">
        <!-- Search -->
        <li
          class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill waves-effect"
        >
          <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
            <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete">
              <div
                class="aa-Autocomplete"
                role="combobox"
                aria-expanded="false"
                aria-haspopup="listbox"
                aria-labelledby="autocomplete-0-label"
              >
                <button
                  type="button"
                  class="btn rounded-pill btn-icon btn-label-primary aa-DetachedSearchButton"
                  title="Pesquisar"
                  id="autocomplete-0-label"
                >
                  <i class="fa-solid fa-magnifying-glass"></i>
                </button>
              </div>
            </span>
          </a>
        </li>
        <!-- /Search -->

        <!-- Style Switcher -->
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill waves-effect"
            id="nav-theme"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            aria-label="Toggle theme (light)"
          >
            <i class="tabler-sun icon-base ti icon-22px theme-icon-active text-heading"></i>
            <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
            <li>
              <button
                type="button"
                class="dropdown-item align-items-center waves-effect active"
                data-bs-theme-value="light"
                aria-pressed="true"
              >
                <span>
                  <i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>
                  Light
                </span>
              </button>
            </li>
            <li>
              <button
                type="button"
                class="dropdown-item align-items-center waves-effect"
                data-bs-theme-value="dark"
                aria-pressed="false"
              >
                <span>
                  <i
                    class="icon-base ti tabler-moon-stars icon-22px me-3"
                    data-icon="moon-stars"
                  ></i>
                  Dark
                </span>
              </button>
            </li>
            <li>
              <button
                type="button"
                class="dropdown-item align-items-center waves-effect"
                data-bs-theme-value="system"
                aria-pressed="false"
              >
                <span>
                  <i
                    class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                    data-icon="device-desktop-analytics"
                  ></i>
                  System
                </span>
              </button>
            </li>
          </ul>
        </li>
        <!-- / Style Switcher-->

        <!-- Quick links  -->
        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
          <a
            class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill waves-effect"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
          >
            <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0">
            <div class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Shortcuts</h6>
                <a
                  href="javascript:void(0)"
                  class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon waves-effect"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  aria-label="Add shortcuts"
                  data-bs-original-title="Add shortcuts"
                >
                  <i class="icon-base ti tabler-plus icon-20px text-heading"></i>
                </a>
              </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container ps">
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-calendar icon-26px text-heading"></i>
                  </span>
                  <a href="app-calendar.html" class="stretched-link">Calendar</a>
                  <small>Appointments</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-file-dollar icon-26px text-heading"></i>
                  </span>
                  <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>
                  <small>Manage Accounts</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-user icon-26px text-heading"></i>
                  </span>
                  <a href="app-user-list.html" class="stretched-link">User App</a>
                  <small>Manage Users</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-users icon-26px text-heading"></i>
                  </span>
                  <a href="app-access-roles.html" class="stretched-link">Role Management</a>
                  <small>Permission</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i
                      class="icon-base ti tabler-device-desktop-analytics icon-26px text-heading"
                    ></i>
                  </span>
                  <a href="index.html" class="stretched-link">Dashboard</a>
                  <small>User Dashboard</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
                  </span>
                  <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>
                  <small>Account Settings</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-help-circle icon-26px text-heading"></i>
                  </span>
                  <a href="pages-faq.html" class="stretched-link">FAQs</a>
                  <small>FAQs &amp; Articles</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base ti tabler-square icon-26px text-heading"></i>
                  </span>
                  <a href="modal-examples.html" class="stretched-link">Modals</a>
                  <small>Useful Popups</small>
                </div>
              </div>
              <div class="ps__rail-x" style="left: 0px; bottom: 0px">
                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px"></div>
              </div>
              <div class="ps__rail-y" style="top: 0px; right: 0px">
                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px"></div>
              </div>
            </div>
          </div>
        </li>
        <!-- Quick links -->

        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
          <a
            class="btn rounded-pill btn-icon btn-label-primary nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill waves-effect"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
          >
            <i class="fa-solid fa-users-rays"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 350px">
            <li class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Notification</h6>
                <div class="d-flex align-items-center h6 mb-0">
                  <span class="badge bg-label-primary me-2">8 New</span>
                  <a
                    href="javascript:void(0)"
                    class="dropdown-notifications-all p-2 btn btn-icon waves-effect waves-light"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    aria-label="Mark all as read"
                    data-bs-original-title="Mark all as read"
                  >
                    <i class="icon-base ti tabler-mail-opened text-heading"></i>
                  </a>
                </div>
              </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container ps">
              <ul class="list-group list-group-flush">
                <li
                  class="list-group-item list-group-item-action dropdown-notifications-item waves-effect"
                >
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                        <img
                          src="../../../public/assets/img/avatars/1.png"
                          alt=""
                          class="rounded-circle"
                        />
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="small mb-1">Congratulation Lettie 🎉</h6>
                      <small class="mb-1 d-block text-body">
                        Won the monthly best seller gold badge
                      </small>
                      <small class="text-body-secondary">1h ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <a href="javascript:void(0)" class="dropdown-notifications-read">
                        <span class="badge badge-dot"></span>
                      </a>
                      <a href="javascript:void(0)" class="dropdown-notifications-archive">
                        <span class="icon-base ti tabler-x"></span>
                      </a>
                    </div>
                  </div>
                </li>
                <li
                  class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read waves-effect"
                >
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                        <img
                          src="../../../public/assets/img/avatars/9.png"
                          alt=""
                          class="rounded-circle"
                        />
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1 small">Application has been approved 🚀</h6>
                      <small class="mb-1 d-block text-body">
                        Your ABC project application has been approved.
                      </small>
                      <small class="text-body-secondary">2 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <a href="javascript:void(0)" class="dropdown-notifications-read">
                        <span class="badge badge-dot"></span>
                      </a>
                      <a href="javascript:void(0)" class="dropdown-notifications-archive">
                        <span class="icon-base ti tabler-x"></span>
                      </a>
                    </div>
                  </div>
                </li>
                <li
                  class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read waves-effect"
                >
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-success">
                          <i class="icon-base ti tabler-chart-pie"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1 small">Monthly report is generated</h6>
                      <small class="mb-1 d-block text-body">
                        July monthly financial report is generated
                      </small>
                      <small class="text-body-secondary">3 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <a href="javascript:void(0)" class="dropdown-notifications-read">
                        <span class="badge badge-dot"></span>
                      </a>
                      <a href="javascript:void(0)" class="dropdown-notifications-archive">
                        <span class="icon-base ti tabler-x"></span>
                      </a>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="ps__rail-x" style="left: 0px; bottom: 0px">
                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px"></div>
              </div>
              <div class="ps__rail-y" style="top: 0px; right: 0px">
                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px"></div>
              </div>
            </li>
            <li class="border-top">
              <div class="d-grid p-4">
                <a
                  class="btn btn-primary btn-sm d-flex waves-effect waves-light"
                  href="javascript:void(0);"
                >
                  <small class="align-middle">View all notifications</small>
                </a>
              </div>
            </li>
          </ul>
        </li>
        <!--/ Notification -->

        <!-- Ícone de três pontos à direita -->
        <li>
          <div class="dropdown">
            <button
              class="btn rounded-pill btn-icon btn-label-primary rounded-pill"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="ti ti-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="javascript:void(0);" @click="showArchiveConfirm">
                  <i class="ti ti-archive me-2"></i>
                  Arquivar
                </a>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <a
                  class="dropdown-item text-danger"
                  href="javascript:void(0);"
                  @click="showDeleteConfirm"
                >
                  <i class="ti ti-trash me-2"></i>
                  Excluir
                </a>
              </li>
            </ul>
          </div>
        </li>
        <!-- / Fim Icones de tres pontos a direita -->

        <!-- Visualizar Arquivados -->
        <label class="switch switch-sm">
          <input type="checkbox" class="switch-input" />
          <span class="switch-toggle-slider">
            <span class="switch-on">
              <i class="icon-base ti tabler-check"></i>
            </span>
            <span class="switch-off">
              <i class="icon-base ti tabler-x"></i>
            </span>
          </span>
          <span class="switch-label">Visualizar cartões arquivados</span>
        </label>
        <!-- / Fim Visualizar Arquivados -->
      </ul>
    </div>
  </div>

  <!-- Debug info -->
  <div v-if="loading" class="alert alert-secondary">Carregando dados...</div>
  <div v-if="error" class="alert alert-danger">Erro ao carregar dados: {{ error }}</div>

  <!-- Conteúdo principal - só aparece após carregar -->
  <template v-if="!loading && !error">
    <div class="scroll-horizontal">
      <!-- Mensagem quando não há colunas -->
      <div v-if="board.columns && board.columns.length === 0">
        <div class="">
          <div class="alert alert-warning">
            <i class="ti ti-columns me-2"></i>
            Nenhuma coluna encontrada para este quadro.
          </div>
          <!-- Botão de adicionar coluna integrado -->
          <div class="" style="min-width: 300px">
            <div v-if="addingColumn" class="w-100">
              <input
                v-model="newColumnName"
                class="form-control mb-2"
                placeholder="Nome da nova coluna"
                @keyup.enter="createColumn"
                @keyup.esc="addingColumn = false"
                ref="newColumnInput"
              />
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary" @click="createColumn">Salvar</button>
                <button class="btn btn-sm btn-secondary" @click="addingColumn = false">
                  Cancelar
                </button>
              </div>
            </div>
            <button v-else class="btn btn-outline-secondary" @click="startAddingColumn">
              + Adicionar Nova Coluna
            </button>
          </div>
        </div>
      </div>

      <!-- Colunas -->
      <main class="kanban-drag">
        <!-- Formulário para adicionar nova coluna - só aparece se houver colunas -->
        <div
          v-if="board.columns && board.columns.length > 0"
          class="d-flex ml-3 flex-column"
          style="min-width: 200px; float: right"
        >
          <div v-if="addingColumn" class="w-100">
            <input
              v-model="newColumnName"
              class="form-control mb-2"
              placeholder="Nome da nova coluna"
              @keyup.enter="createColumn"
              @keyup.esc="addingColumn = false"
            />
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-primary" @click="createColumn">Salvar</button>
              <button class="btn btn-sm btn-secondary" @click="addingColumn = false">
                Cancelar
              </button>
            </div>
          </div>
          <button v-else class="btn btn-outline-secondary" @click="startAddingColumn">
            + Adicionar Nova Coluna
          </button>
        </div>
        <!-- FIM Formulário para adicionar nova coluna -->
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
                maxHeight: 'calc(100vh - 190px)',
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
                      <!-- Exibição das etiquetas no card -->
                      <div v-if="card.labels && card.labels.length > 0" class="card-labels mb-2">
                        <span
                          v-for="label in card.labels"
                          :key="label.id"
                          class="badge me-1 mb-1"
                          :style="{
                            backgroundColor: label.color,
                            color: 'white',
                            fontSize: '0.75rem',
                            padding: '0.25rem 0.5rem',
                          }"
                        >
                          {{ label.name }}
                        </span>
                      </div>
                      <!-- / Exibição das etiquetas no card -->
                      <!-- <span
                        class="badge position-absolute top-0 end-0 m-1"
                        :class="`bg-${card.color || 'secondary'}`"
                      >
                        &nbsp;
                      </span> -->
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

                      <div
                        class="d-flex align-items-center gap-1 mt-2 icons-cards small text-muted"
                      >
                        <i
                          v-if="hasRealContent(card.full_description)"
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
                  <button
                    class="btn btn-sm btn-label-secondary"
                    @click="showAddCardForm(column.id)"
                  >
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
                    <button class="btn btn-sm btn-secondary" @click="cancelAddCard">
                      Cancelar
                    </button>
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

      <ConfirmModal
        :show="showConfirmModal"
        :title="confirmModalConfig.title"
        :message="confirmModalConfig.message"
        :confirmText="confirmModalConfig.confirmText"
        :danger="confirmModalConfig.danger"
        @confirm="handleConfirm"
        @cancel="showConfirmModal = false"
      />
    </div>
  </template>
</template>

<script>
  //remove o scroll do navegador
  export default {
    name: 'AppKanban',
    mounted() {
      document.body.classList.add('no-scroll');
    },
    beforeUnmount() {
      document.body.classList.remove('no-scroll');
    },
  };
</script>

<script setup>
  import { ref, onMounted, onBeforeUnmount, nextTick, inject } from 'vue';
  import axios from 'axios';
  import draggable from 'vuedraggable';
  import CardModal from './CardModal.vue';
  import ColumnMenu from './ColumnMenu.vue';
  import ConfirmModal from './ConfirmModal.vue';

  // props e emits
  const props = defineProps({ boardId: Number });
  const emit = defineEmits(['update:modelValue']);

  // utilitário para saber se há texto de verdade no editor
  const hasRealContent = content => {
    if (!content || content.trim() === '' || content === '<p><br></p>') {
      return false;
    }
    // Remove tags HTML e verifica se sobra algum texto
    const textOnly = content
      .replace(/<[^>]*>/g, '')
      .replace(/&nbsp;/g, '')
      .trim();
    return textOnly.length > 0;
  };

  // pegar o boardUuid via provide/inject
  const boardUuid = inject('boardUuid');

  // estado reativo
  const board = ref({ title: '', columns: [] });
  const selectedCardId = ref(null);
  const showModal = ref(false);
  const loading = ref(false);
  const error = ref(null);
  const showConfirmModal = ref(false);
  const confirmAction = ref('');
  const confirmModalConfig = ref({ title: '', message: '', confirmText: '', danger: false });

  onMounted(async () => {
    document.body.classList.add('no-scroll');
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

  onBeforeUnmount(() => {
    document.body.classList.remove('no-scroll');
  });

  const loadBoard = async () => {
    loading.value = true;
    error.value = null;

    try {
      await axios.get('/sanctum/csrf-cookie');
      const { data } = await axios.get(`/api/boards/${boardUuid}`);

      // DEBUG: Verificar se as labels estão vindo
      console.log('Board completo:', data);
      if (data.columns && data.columns.length > 0) {
        const firstColumn = data.columns[0];
        if (firstColumn.cards && firstColumn.cards.length > 0) {
          console.log('Primeiro card da primeira coluna:', firstColumn.cards[0]);
          console.log('Labels do primeiro card:', firstColumn.cards[0].labels);
        }
      }

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
      const { data } = await axios.get(`/api/boards/${boardUuid}/columns`);
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

  function startAddingColumn() {
    addingColumn.value = true;
    nextTick(() => {
      // Tenta focar no input após ele ser renderizado
      const inputs = document.querySelectorAll('input[placeholder="Nome da nova coluna"]');
      if (inputs.length > 0) {
        inputs[inputs.length - 1].focus(); // Foca no último input (o mais recente)
      }
    });
  }

  async function createColumn() {
    if (!newColumnName.value.trim()) {
      alert('Por favor, insira um nome para a coluna');
      return;
    }

    try {
      console.log('Criando coluna:', {
        board_id: board.value.id,
        name: newColumnName.value,
        order: board.value.columns ? board.value.columns.length : 0,
      });

      const res = await axios.post('/api/columns', {
        board_id: board.value.id,
        name: newColumnName.value,
        order: board.value.columns ? board.value.columns.length : 0,
      });

      console.log('Resposta:', res.data);

      // Verifica o formato da resposta
      const newCol = res.data.column || res.data;

      // Adiciona propriedade cards se não existir
      if (!newCol.cards) {
        newCol.cards = [];
      }

      // Inicializa o array se não existir
      if (!board.value.columns) {
        board.value.columns = [];
      }

      board.value.columns.push(newCol);
      newColumnName.value = '';
      addingColumn.value = false;

      // Salva no localStorage se existir
      if (window.localStorage) {
        window.localStorage.setItem(`board_${board.value.id}`, JSON.stringify(board.value));
      }
    } catch (err) {
      console.error('Erro ao criar coluna:', err);
      console.error('Resposta de erro:', err.response);
      alert('Erro ao criar coluna: ' + (err.response?.data?.message || err.message));
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

  // Função confirmação arquivar ou excluir quadro *********************************************/
  const showArchiveConfirm = () => {
    confirmAction.value = 'archive';
    confirmModalConfig.value = {
      title: 'Arquivar quadro?',
      message: 'O quadro será movido para os arquivados e poderá ser restaurado posteriormente.',
      confirmText: 'Arquivar',
      danger: false,
    };
    showConfirmModal.value = true;
  };

  const showDeleteConfirm = () => {
    confirmAction.value = 'delete';
    confirmModalConfig.value = {
      title: 'Remover este quadro?',
      message: 'Esta ação é irreversível.',
      confirmText: 'Remover',
      danger: true,
    };
    showConfirmModal.value = true;
  };

  const handleConfirm = async () => {
    showConfirmModal.value = false;

    try {
      if (confirmAction.value === 'archive') {
        await axios.patch(`/api/boards/${boardUuid}/archive`);
        window.location.href = '/tarefas-vue';
      } else if (confirmAction.value === 'delete') {
        await axios.delete(`/api/boards/${boardUuid}`);
        window.location.href = '/tarefas-vue';
      }
    } catch (err) {
      console.error('Erro detalhado:', err.response);
      alert(
        `Erro ao ${confirmAction.value === 'archive' ? 'arquivar' : 'excluir'} o quadro: ${err.response?.data?.message || err.message}`
      );
    }
  };

  async function archiveBoard() {
    try {
      const response = await axios.patch(`/api/boards/${board.value.uuid}/archive`);
      alert('Quadro arquivado com sucesso!');
      window.location.href = '/tarefas-vue'; // Alterado de '/boards' para '/tarefas-vue'
    } catch (error) {
      console.error('Erro completo:', error.response);
      alert('Erro ao arquivar o quadro: ' + (error.response?.data?.error || error.message));
    }
  }

  async function deleteBoard() {
    try {
      const response = await axios.delete(`/api/boards/${board.value.uuid}`);
      alert('Quadro excluído com sucesso!');
      window.location.href = '/tarefas-vue'; // Alterado de '/boards' para '/tarefas-vue'
    } catch (error) {
      console.error('Erro completo:', error.response);
      alert('Erro ao excluir o quadro: ' + (error.response?.data?.error || error.message));
    }
  }
  // FIM Função confirmação arquivar ou excluir quadro *****************************************/

  // FUnçao abrir as labels (etiquetas) corretamete
  const openCard = card => {
    console.log('Abrindo card com labels:', card.labels);

    selectedCard.value = {
      ...card,
      labels: card.labels || [], // Garante que sempre há um array
    };

    showCardModal.value = true;
  };
  // Fim Função abrir as labels corretamente
</script>

<!-- estilo GLOBAL, não scoped -->
<style>
  body.no-scroll {
    overflow: hidden !important;
    background: #e0deea;
  }
</style>

<style scoped>
  .scroll-horizontal {
    width: 100%; /* Pega a largura total disponível do container pai */
    max-width: 100vw; /* Limita à largura da viewport */
    overflow-x: auto; /* Cria scroll horizontal quando necessário */
    overflow-y: hidden; /* Evita scroll vertical */
    -webkit-overflow-scrolling: touch; /* Scroll suave no iOS */
    scrollbar-width: thin; /* Firefox */
    padding: 0px 0px 30px 0px;
  }

  /* Para garantir que o conteúdo interno (kanban) mantenha sua largura */
  .scroll-horizontal > * {
    display: inline-block; /* Ou inline-flex se for o caso */
    vertical-align: top;
  }

  /* Estilização opcional da barra de scroll */
  .scroll-horizontal::-webkit-scrollbar {
    height: 10px;
  }

  .scroll-horizontal::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 5px;
  }

  .scroll-horizontal::-webkit-scrollbar-thumb {
    background: #000;
    border-radius: 5px;
  }

  .scroll-horizontal::-webkit-scrollbar-thumb:hover {
    background: #000;
  }
  .kanban-board-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .kanban-wrapper {
    min-height: 400px;
  }

  .kanban-board-columns {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
  }

  .kanban-add-column {
    min-width: 280px;
    display: flex;
    align-items: flex-start;
    padding-top: 0.5rem;
  }

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
