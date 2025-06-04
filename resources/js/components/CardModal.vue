<template>
  <transition name="modal-fade">
    <div class="modal fade show d-block" tabindex="-1" ref="modalEl">
      <div class="modal-dialog modal-xl">
        <div
          class="overlay-modal-anexo"
          v-if="card"
          @dragover.prevent="handleDragOver"
          @dragleave.prevent="handleDragLeave"
          @drop.prevent="handleDrop"
        >
          <div class="modal-content bg-transparent">
            <div
              v-if="loadingCard"
              class="d-flex justify-content-center align-items-center"
              style="height: 400px"
            >
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
              </div>
            </div>

            <div v-else class="card-modal-wrapper d-flex flex-column flex-md-row">
              <!-- LADO ESQUERDO -->
              <div class="card-main p-5 m-5 flex-grow-1">
                <!-- Campo TÍTULO -->
                <div class="mb-0">
                  <h2
                    v-if="!editing.title"
                    class="h3 text-black mb-0 pl-0"
                    @click="startEdit('title')"
                  >
                    {{ card.title || '(vazio)' }}
                    <i class="fa-solid fa-pencil small cor_icone"></i>
                  </h2>

                  <template v-else>
                    <input
                      v-model="localCard.title"
                      class="editable-field bg-transparent pl-2 w-100 h3 font-size-sm text-black mb-0 pb-0"
                    />
                    <div class="mt-2">
                      <button
                        class="btn btn-xs btn-primary me-2 mb-2 rounded shadow-none"
                        @click="saveField('title')"
                        :disabled="loadingField === 'title'"
                      >
                        <span v-if="loadingField === 'title'" class="custom-spinner me-1" />
                        Salvar
                      </button>
                      <button
                        class="btn btn-xs btn-label-secondary rounded mb-2"
                        @click="cancelEdit('title')"
                      >
                        Cancelar
                      </button>
                    </div>
                  </template>
                </div>
                <!-- FIM Campo TÍTULO -->

                <small class="cor_icone d-block mb-3">
                  {{ card.user?.name || 'Desconhecido' }} adicionou este card em
                  {{ formatDate(card.created_at) }}
                </small>

                <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                  <div
                    v-if="card.start_date"
                    class="badge bg-secondary text-white d-flex align-items-center"
                  >
                    <i class="fa-regular fa-calendar me-1"></i>
                    Início: {{ formatDate(card.start_date) }}
                  </div>
                  <div
                    v-if="card.due_date"
                    :class="[
                      'badge d-flex align-items-center',
                      isOverdue(card.due_date) ? 'bg-danger' : 'bg-success',
                    ]"
                  >
                    <i class="fa-regular fa-clock me-1"></i>
                    Entrega: {{ formatDate(card.due_date) }}
                  </div>
                </div>

                <!-- Campos editáveis -->

                <!-- Campo mini DESCRIÇÃO: esse campo aparece na listagem dos CARDS -->
                <!--
              <div class="mb-3 mt-5">
                <i class="fa-solid fa-align-left me-2 cor_icone"></i>
                <label class="form-label">Descrição</label>
                <div
                  v-if="!editing.description"
                  class="editable-field border rounded p-2"
                  @click="startEdit('description')"
                >
                  {{ card.description || '(vazio)' }}
                </div>
                <template v-else>
                  <input v-model="localCard.description" class="form-control" />
                  <div class="mt-2">
                    <button
                      class="btn btn-sm btn-primary me-2"
                      @click="saveField('description')"
                      :disabled="loadingField === 'description'"
                    >
                      <span v-if="loadingField === 'description'" class="custom-spinner me-1" />
                      Salvar
                    </button>
                    <button class="btn btn-sm btn-secondary" @click="cancelEdit('description')">
                      Cancelar
                    </button>
                  </div>
                </template>
              </div>
              -->
                <!-- Campo mini DESCRIÇÃO: esse campo aparece na listagem dos CARDS -->

                <!-- Campo DESCRIÇÃO COMPLETA -->
                <div class="mb-3 mt-5 pt-4">
                  <i class="fa-solid fa-align-left me-2 cor_icone"></i>
                  <label class="form-label">Descrição</label>

                  <!-- Exibição da descrição (modo leitura) -->
                  <div v-if="!editing.full_description">
                    <div
                      class="text-area-editor formatted-content text-black p-2"
                      :class="{ 'collapsed-content': !showFullDescription && isLongDescription }"
                      @click="handleContentClick"
                      v-html="formattedDescription"
                    ></div>

                    <!-- Botão Mostrar mais/menos -->
                    <div v-if="isLongDescription" class="text-center mt-2">
                      <button
                        class="btn btn-sm btn-link text-decoration-none"
                        @click.stop="toggleDescription"
                      >
                        <i
                          :class="
                            showFullDescription
                              ? 'fa-solid fa-chevron-up'
                              : 'fa-solid fa-chevron-down'
                          "
                          class="me-2"
                        ></i>
                        {{ showFullDescription ? 'Mostrar menos' : 'Mostrar mais' }}
                      </button>
                    </div>
                  </div>

                  <!-- Modo edição com RichTextEditor -->
                  <template v-else>
                    <RichTextEditor v-model="localCard.full_description" class="mb-2" rows="1" />

                    <div class="mt-2">
                      <button
                        class="btn btn-xs btn-primary me-2 mb-2 rounded shadow-none"
                        @click="saveField('full_description')"
                        :disabled="loadingField === 'full_description'"
                      >
                        <span
                          v-if="loadingField === 'full_description'"
                          class="custom-spinner me-1"
                        />
                        Salvar
                      </button>
                      <button
                        class="btn btn-xs btn-label-secondary me-2 mb-2 rounded"
                        @click="cancelEdit('full_description')"
                      >
                        Cancelar
                      </button>
                    </div>
                  </template>
                </div>

                <!-- Campo LINK -->
                <!--
                <div class="mb-3">
                  <label class="form-label">Link</label>
                  <div
                    v-if="!editing.link"
                    class="editable-field border rounded p-2"
                    @click="startEdit('link')"
                  >
                    {{ card.link || '(vazio)' }}
                  </div>
                  <template v-else>
                    <input v-model="localCard.link" class="form-control" />
                    <div class="mt-2">
                      <button
                        class="btn btn-sm btn-primary me-2"
                        @click="saveField('link')"
                        :disabled="loadingField === 'link'"
                      >
                        <span v-if="loadingField === 'link'" class="custom-spinner me-1" />
                        Salvar
                      </button>
                      <button class="btn btn-sm btn-secondary" @click="cancelEdit('link')">
                        Cancelar
                      </button>
                    </div>
                  </template>
                </div>
                -->

                <!-- Campo COR -->
                <div class="mb-3">
                  <label class="form-label">Cor</label>
                  <div
                    v-if="!editing.color"
                    class="editable-field border rounded p-2"
                    @click="startEdit('color')"
                  >
                    {{ card.color || '(vazio)' }}
                  </div>
                  <template v-else>
                    <select v-model="localCard.color" class="form-select">
                      <option disabled value="">Selecione uma cor</option>
                      <option value="primary">Primary</option>
                      <option value="success">Success</option>
                      <option value="danger">Danger</option>
                      <option value="warning">Warning</option>
                      <option value="secondary">Secondary</option>
                    </select>
                    <div class="mt-2">
                      <button
                        class="btn btn-xs btn-primary me-2 mb-2 rounded shadow-none"
                        @click="saveField('color')"
                        :disabled="loadingField === 'color'"
                      >
                        <span v-if="loadingField === 'color'" class="custom-spinner me-1" />
                        Salvar
                      </button>
                      <button
                        class="btn btn-xs btn-label-secondary me-2 mb-2 rounded"
                        @click="cancelEdit('color')"
                      >
                        Cancelar
                      </button>
                    </div>
                  </template>
                </div>

                <!-- Upload e anexos -->
                <div class="mb-3">
                  <label class="form-label">Anexos</label>
                  <div class="upload-area" id="upload-area" @click="abrirSeletor">
                    <i class="fas fa-cloud-upload-alt text-muted"></i>
                    Arraste arquivos aqui ou clique para selecionar.
                    <small class="text-muted"><i>(Máximo 10MB por arquivo)</i></small>
                  </div>
                  <!-- Seu campo original, apenas escondido -->
                  <input
                    type="file"
                    ref="fileInput"
                    class="form-control form-control-sm mb-2"
                    style="display: none"
                    @change="uploadAttachments"
                    multiple
                  />
                  <div v-if="uploadError" class="text-danger mt-1">{{ uploadError }}</div>
                  <div v-if="uploading" class="d-flex align-items-center gap-2 mt-2">
                    <span class="custom-spinner"></span>
                    <small>Enviando arquivo...</small>
                  </div>
                </div>

                <ul class="list-group mb-4">
                  <li
                    v-for="att in card.attachments || []"
                    :key="att.id"
                    class="list-group-item d-flex justify-content-between align-items-center position-relative"
                  >
                    <div class="d-flex align-items-center gap-3">
                      <div
                        class="anexo-preview d-flex align-items-center justify-content-center rounded"
                      >
                        <template v-if="att.mime_type.startsWith('image')">
                          <img
                            :src="`/storage/${att.path}`"
                            alt="preview"
                            class="anexo-img"
                            @click="openPreview(att)"
                          />
                        </template>
                        <template v-else>
                          <div class="anexo-ext">{{ getExtension(att.filename) }}</div>
                        </template>
                      </div>
                      <div>
                        <a
                          :href="`/attachments/${att.id}/download`"
                          class="fw-semibold d-block small"
                          target="_blank"
                        >
                          {{ att.filename }}
                        </a>
                        <small class="text-muted">
                          Adicionado em {{ formatDate(att.created_at) }}
                        </small>
                      </div>
                    </div>
                    <div class="dropdown">
                      <button
                        class="btn bg-light text-muted ml-5 py-2 px-3"
                        type="button"
                        data-bs-toggle="dropdown"
                      >
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <a class="dropdown-item" :href="`/attachments/${att.id}/download`">
                            Baixar
                          </a>
                        </li>
                        <li>
                          <button
                            class="dropdown-item text-danger"
                            type="button"
                            @click="confirmingAttachmentId = att.id"
                          >
                            Excluir
                          </button>
                        </li>
                      </ul>
                    </div>
                    <!-- Popover de confirmação -->
                    <transition name="fade-popover">
                      <div
                        v-if="confirmingAttachmentId === att.id"
                        class="popover-box p-2 shadow border rounded bg-white position-absolute end-0"
                        style="top: 100%; z-index: 1055; min-width: 220px"
                      >
                        <p class="mb-2">
                          Remover este anexo?
                          <br />
                          <small>Esta ação é irreversível.</small>
                        </p>
                        <div class="d-flex justify-content-end gap-2">
                          <button class="btn btn-sm btn-danger" @click="deleteAttachment(att.id)">
                            Remover
                          </button>
                          <button class="btn btn-sm btn-secondary" @click="cancelConfirmAttachment">
                            Cancelar
                          </button>
                        </div>
                      </div>
                    </transition>
                  </li>
                </ul>

                <!-- Comentários com Editor Expansível -->
                <div class="mb-3">
                  <label class="form-label">Atividade</label>

                  <!-- Campo simples que expande para editor -->
                  <div v-if="!showCommentEditor" class="comment-input-wrapper">
                    <textarea
                      class="form-control comment-placeholder"
                      placeholder="Escrever um comentário..."
                      rows="1"
                      @click="openCommentEditor"
                      @focus="openCommentEditor"
                      readonly
                    ></textarea>
                  </div>

                  <!-- Editor Quill expandido -->
                  <div v-else class="comment-editor-expanded">
                    <div class="comment-editor-wrapper mb-2">
                      <RichTextEditor
                        v-model="newComment"
                        placeholder="Escrever um comentário..."
                        height="100px"
                        ref="commentEditorRef"
                      />
                    </div>

                    <div class="d-flex gap-2 mt-2 mb-5 pb-5">
                      <button
                        class="btn btn-xs btn-primary me-2 mb-2 rounded shadow-none"
                        @click="addComment"
                        :disabled="!newComment.trim() || newComment === '<p><br></p>'"
                      >
                        Comentar
                      </button>
                      <button
                        class="btn btn-xs btn-label-secondary me-2 mb-2 rounded"
                        @click="closeCommentEditor"
                      >
                        Cancelar
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="card.comments && card.comments.length">
                  <div
                    v-for="comment in sortedComments"
                    :key="comment.id"
                    class="comment-item d-flex gap-3 mb-3"
                  >
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ getInitials(comment.user.name) }}
                      </span>
                    </div>
                    <div class="comment-content w-100 p-3 border rounded">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="cor-texto">{{ comment.user.name }}</small>
                        <small class="cor-texto">
                          {{ formatDate(comment.created_at) }}
                          <span v-if="comment.updated_at !== comment.created_at">(editado)</span>
                        </small>
                      </div>

                      <!-- Modo visualização do comentário -->
                      <div v-if="editingCommentId !== comment.id">
                        <div
                          class="comment-text formatted-content pt-2"
                          v-html="processCommentContent(comment.content)"
                          @click="handleCommentClick"
                        ></div>
                        <div class="comment-actions text-muted small mt-2 border-top pt-2">
                          <a
                            href="#"
                            class="btn btn-xs btn-comments p-0 me-3 rounded"
                            @click.prevent="startEditComment(comment)"
                          >
                            Editar
                          </a>
                          <a
                            href="#"
                            class="btn btn-xs btn-comments me-2 p-0 rounded"
                            @click.prevent="confirmingCommentId = comment.id"
                          >
                            Excluir
                          </a>
                        </div>
                      </div>

                      <!-- Modo edição do comentário -->
                      <div v-else>
                        <div class="comment-editor-wrapper mb-2">
                          <RichTextEditor v-model="editedComment" height="120px" />
                        </div>
                        <div class="d-flex gap-2">
                          <button
                            class="btn btn-xs btn-primary me-2 mb-2 rounded shadow-none"
                            @click="saveEditedComment(comment.id)"
                            :disabled="!editedComment.trim() || editedComment === '<p><br></p>'"
                          >
                            Salvar
                          </button>
                          <button
                            class="btn btn-xs btn-label-secondary me-2 mb-2 rounded"
                            @click="cancelEditComment"
                          >
                            Cancelar
                          </button>
                        </div>
                      </div>

                      <!-- Popover de confirmação -->
                      <transition name="fade-popover">
                        <div
                          v-if="confirmingCommentId === comment.id"
                          class="popover-box p-2 shadow border rounded bg-white position-absolute end-0"
                          style="top: 100%; z-index: 1055; min-width: 220px"
                        >
                          <p class="mb-2">
                            Remover este comentário?
                            <br />
                            <small>Esta ação é irreversível.</small>
                          </p>
                          <div class="d-flex justify-content-end gap-2">
                            <button
                              class="btn btn-sm btn-danger"
                              @click="deleteComment(comment.id)"
                            >
                              Remover
                            </button>
                            <button
                              class="btn btn-sm btn-secondary"
                              @click="confirmingCommentId = null"
                            >
                              Cancelar
                            </button>
                          </div>
                        </div>
                      </transition>
                    </div>
                  </div>
                </div>
                <!-- Fim Comentários -->
                <div v-else class="cor-texto">
                  <i>Nenhum comentário. Seja o primeiro a comentar!</i>
                </div>

                <!-- Feedback visual de drag-and-drop -->
                <transition name="fade">
                  <div
                    v-if="draggingOver"
                    class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-75 text-white"
                    style="z-index: 1055"
                    @dragenter.prevent
                    @dragleave.prevent="handleDragLeave"
                    @drop.prevent="handleDrop"
                  >
                    <div class="text-center">
                      <h5 class="text-light">Solte os arquivos para fazer upload</h5>
                      <p class="small">(Limite: 10MB por arquivo)</p>
                    </div>
                  </div>
                </transition>
                <!-- FIM Feedback visual de drag-and-drop -->
              </div>

              <!-- LADO DIREITO -->
              <div class="col-md-2 p-3 m-3 card-sidebar">
                <div class="d-flex  justify-content-end">
                <button class="btn btn-xs btn-outline-secondary border-none mb-2 p-1" @click="close">  <i class="fas fa-close me-1"></i>  </button>
                </div>
                <div class="text-center">
                  <h6 class="font-bold mb-5 ">Ações do Card</h6>
                </div>
                <!-- <button class="btn-close d-flex align-items-center" @click="close"></button> -->
                <!-- Botão Datas -->
                <button class="btn btn-sm rounded-pill btn-outline-secondary waves-effect w-100 mb-2 text-start" @click.stop="toggleDatePanel">
                  <i class="fa-regular fa-calendar me-2"></i>
                  Datas
                </button>

                <DatePopover
                  v-if="showDatePanel"
                  :card="card"
                  @close="showDatePanel = false"
                  @updated="handleDateUpdate"
                />
                <!-- FIM Botão Datas -->

                <!-- Botão Etiquetas -->
                <button class="btn btn-sm rounded-pill btn-outline-secondary waves-effect w-100 mb-2 text-start" @click.stop="toggleLabelsPanel">
                  <i class="fa-solid fa-tag me-2"></i>
                  Etiquetas
                  <span
                    v-if="card.labels && card.labels.length > 0"
                    class="badge bg-secondary ms-2"
                  >
                    {{ card.labels.length }}
                  </span>
                </button>

                <LabelsPopover
                  v-if="showLabelsPanel"
                  :card="card"
                  @close="showLabelsPanel = false"
                  @updated="handleLabelsUpdate"
                />
                <!-- FIM Botão Etiquetas -->


                <!-- Botões Arquivar -->
                <button class="btn btn-sm rounded-pill btn-outline-secondary  waves-effect w-100 mb-2 text-start" @click="toggleConfirmArchive">
                  <i class="fas fa-archive me-1"></i>
                  Arquivar
                </button>
                <transition name="fade-popover">
                  <div
                    v-if="showArchiveConfirm"
                    class="popover-box p-2 shadow border rounded bg-white  end-0"
                    style="bottom: 100%; z-index: 1055; min-width: 240px"
                  >
                    <p class="mb-2">Deseja arquivar ou excluir este card?</p>
                    <div class="d-flex justify-content-end gap-2">
                      <button class="btn btn-xs rounded btn-warning" @click="archiveCard">Arquivar</button>
                      <button class="btn btn-xs rounded btn-danger" @click="deleteCard">Excluir</button>
                      <button class="btn btn-xs rounded btn-secondary" @click="cancelArchiveConfirm">
                        Cancelar
                      </button>
                    </div>
                  </div>
                </transition>
                <!-- Fim Botões Arquivar -->
              </div>
              <!-- Fim LADO DIREITO -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>

  <!-- Lightbox -->
  <VueEasyLightbox
    :visible="visibleRef"
    :imgs="imgsRef"
    :index="indexRef"
    @hide="visibleRef = false"
  />
</template>

<script setup>
  import { ref, watch, onMounted, nextTick, computed } from 'vue';
  import { Modal } from 'bootstrap';
  import axios from 'axios';
  import VueEasyLightbox from 'vue-easy-lightbox';
  import DatePopover from './DatePopover.vue';
  import RichTextEditor from './RichTextEditor.vue';
  import LabelsPopover from './LabelsPopover.vue';

  const props = defineProps({
    cardId: Number,
  });

  // PRELOADERS
  const loadingCard = ref(true);
  const loadingField = ref(null);
  const uploading = ref(false);
  // fim PRELOADERS

  const emit = defineEmits(['close', 'update-card', 'remove-card']);
  const visibleRef = ref(false);
  const indexRef = ref(0);
  const imgsRef = ref([]);

  const modalEl = ref(null);
  let modal = null;
  const card = ref(null);
  const localCard = ref({});
  const editing = ref({});
  const uploadError = ref(null);
  const confirmingAttachmentId = ref(null);
  const showArchiveConfirm = ref(false);
  const fileInput = ref(null);

  const newComment = ref('');
  const editingCommentId = ref(null);
  const editedComment = ref('');
  const confirmingCommentId = ref(null);
  const showCommentEditor = ref(false);
  const commentEditorRef = ref(null);

  const showFullDescription = ref(false);

  /* VARIÁVEL PARA O PAINEL DE DATAS */
  const showDatePanel = ref(false);

  /* Labels */
  const showLabelsPanel = ref(false);

  const draggingOver = ref(false);

  /* MOSTRA A INICIAL DO NOME DO USUÁRIO */
  const userName = ref('Usuário');
  const getInitials = name => {
    return name
      .split(' ')
      .map(word => word[0])
      .slice(0, 2)
      .join('')
      .toUpperCase();
  };
  /* FIM MOSTRA A INICIAL DO NOME DO USUÁRIO */

  const fields = [
    { key: 'title', label: 'Título' },
    { key: 'description', label: 'Descrição' },
    { key: 'full_description', label: 'Descrição Completa' },
    { key: 'link', label: 'Link' },
    { key: 'color', label: 'Cor' },
  ];

  const fetchCard = async () => {
    if (props.cardId) {
      try {
        const { data } = await axios.get(`/api/cards/${props.cardId}`);
        console.log('Card retornado da API:', data);
        console.log('Comentários recebidos:', data.comments);

        // Verificar se comments existe e é um array
        if (!data.comments) {
          console.warn('Os comentários não foram retornados da API ou não estão definidos');
          data.comments = []; // Inicializa como array vazio caso não exista
        } else if (!Array.isArray(data.comments)) {
          console.warn('Os comentários não são um array:', data.comments);
          data.comments = []; // Força como array vazio se não for um array
        }

        card.value = {
          ...data,
          start_date: data.start_date || null,
          due_date: data.due_date || null,
          reminder_interval: data.reminder_interval || '',
        };

        card.value = data;
        localCard.value = JSON.parse(JSON.stringify(data));
      } catch (error) {
        console.error('Erro ao buscar dados do card:', error);
      }
    }
  };

  const startEdit = key => {
    editing.value[key] = true;
  };

  const cancelEdit = key => {
    localCard.value[key] = card.value[key];
    editing.value[key] = false;
  };

  const saveField = async key => {
    // Debug - verificar o que está sendo salvo
    console.log('=== DEBUG SAVE FIELD ===');
    console.log('Campo:', key);
    console.log('Valor a salvar:', localCard.value[key]);
    console.log('Tipo do valor:', typeof localCard.value[key]);

    loadingField.value = key;

    try {
      const updated = { [key]: localCard.value[key] };

      // Debug - verificar o objeto que será enviado
      console.log('Objeto enviado para API:', updated);

      // Salva apenas o campo alterado
      const response = await axios.put(`/api/cards/${card.value.id}`, updated);

      // Debug - verificar resposta
      console.log('Resposta da API:', response.data);

      // Atualiza apenas o campo no card atual, sem recarregar tudo
      card.value[key] = localCard.value[key];

      editing.value[key] = false;

      // Emite para atualizar o card no board (caso necessário)
      emit('update-card', card.value);

      console.log('Campo salvo com sucesso!');
      console.log('=== FIM DEBUG ===');
    } catch (error) {
      console.error(`Erro ao salvar o campo "${key}":`, error);
      console.error('Detalhes do erro:', error.response?.data);
    } finally {
      loadingField.value = null;
    }
  };

  const uploadAttachments = async e => {
    const files = e.target.files;
    uploadError.value = null;

    if (!files.length) return;

    uploading.value = true;

    function abrirSeletor() {
      if (fileInput.value) {
        console.log('Disparando clique no input');
        fileInput.value.click();
      } else {
        console.warn('fileInput.value está nulo');
      }
    }

    // Array para armazenar erros específicos
    const errors = [];
    let successCount = 0;

    try {
      for (const file of files) {
        // Validação de tamanho
        if (file.size > 10 * 1024 * 1024) {
          errors.push(
            `${file.name}: excede o limite de 10MB (${(file.size / 1024 / 1024).toFixed(2)}MB)`
          );
          continue; // continua para o próximo arquivo
        }

        try {
          const form = new FormData();
          form.append('file', file);

          // Log para debug
          console.log(`Enviando arquivo: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`);

          const { data } = await axios.post(`/api/cards/${card.value.id}/attachments`, form, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
            // Timeout maior para arquivos grandes
            timeout: 60000, // 60 segundos
            // Callback para progresso (opcional)
            onUploadProgress: progressEvent => {
              const percentCompleted = Math.round(
                (progressEvent.loaded * 100) / progressEvent.total
              );
              console.log(`Upload ${file.name}: ${percentCompleted}%`);
            },
          });

          if (!card.value.attachments) card.value.attachments = [];
          card.value.attachments.push(data.attachment);
          localCard.value.attachments = [...card.value.attachments];

          successCount++;
          console.log(`✓ ${file.name} enviado com sucesso`);
        } catch (fileError) {
          // Captura erro específico do arquivo
          console.error(`Erro ao enviar ${file.name}:`, fileError);

          let errorMessage = `${file.name}: `;

          // Trata diferentes tipos de erro
          if (fileError.response) {
            // Erro do servidor
            const status = fileError.response.status;
            const serverMessage =
              fileError.response.data?.message || fileError.response.data?.error;

            switch (status) {
              case 413:
                errorMessage += 'arquivo muito grande para o servidor';
                break;
              case 415:
                errorMessage += 'tipo de arquivo não permitido';
                break;
              case 422:
                errorMessage += serverMessage || 'arquivo inválido';
                break;
              case 500:
                errorMessage += 'erro no servidor';
                break;
              default:
                errorMessage += serverMessage || `erro ${status}`;
            }
          } else if (fileError.code === 'ECONNABORTED') {
            errorMessage += 'tempo limite excedido (arquivo muito grande ou conexão lenta)';
          } else if (fileError.message) {
            errorMessage += fileError.message;
          } else {
            errorMessage += 'erro desconhecido';
          }

          errors.push(errorMessage);
        }
      }

      // Atualiza o card se pelo menos um arquivo foi enviado
      if (successCount > 0) {
        emit('update-card', card.value);
      }

      // Mostra mensagem de sucesso/erro
      if (errors.length > 0 && successCount > 0) {
        uploadError.value = `${successCount} arquivo(s) enviado(s). Erros:\n${errors.join('\n')}`;
      } else if (errors.length > 0) {
        uploadError.value = `Erro ao enviar arquivo(s):\n${errors.join('\n')}`;
      } else {
        // Todos enviados com sucesso - opcional: mostrar mensagem de sucesso
        console.log(`✓ Todos os ${successCount} arquivo(s) enviados com sucesso!`);
      }
    } catch (err) {
      // Erro geral não esperado
      console.error('Erro geral ao enviar anexos:', err);
      uploadError.value = 'Erro inesperado ao processar arquivos.';
    } finally {
      uploading.value = false;
      if (fileInput.value) {
        fileInput.value.value = null; // limpa o campo
      }
    }
  };

  const toggleConfirmAttachment = id => {
    confirmingAttachmentId.value = confirmingAttachmentId.value === id ? null : id;
  };

  const cancelConfirmAttachment = () => {
    confirmingAttachmentId.value = null;
  };

  const deleteAttachment = async id => {
    try {
      await axios.delete(`/api/attachments/${id}`);
      card.value.attachments = card.value.attachments.filter(att => att.id !== id);
      localCard.value.attachments = [...card.value.attachments];
      emit('update-card', card.value);
      confirmingAttachmentId.value = null;
    } catch (error) {
      console.error('Erro ao excluir anexo:', error);
    }
  };

  const toggleConfirmArchive = () => {
    showArchiveConfirm.value = !showArchiveConfirm.value;
  };

  const cancelArchiveConfirm = () => {
    showArchiveConfirm.value = false;
  };

  const archiveCard = async () => {
    try {
      const { data } = await axios.put(`/api/cards/${card.value.id}`, { status: 1 });
      card.value = data;
      emit('remove-card', card.value.id);
      close();
    } catch (error) {
      console.error('Erro ao arquivar card:', error);
    }
  };

  const deleteCard = async () => {
    try {
      const { data } = await axios.put(`/api/cards/${card.value.id}`, { status: 5 });
      card.value = data;
      emit('remove-card', card.value.id);
      close();
    } catch (error) {
      console.error('Erro ao excluir card:', error);
    }
  };

  const close = () => {
    if (modal) modal.hide();
    emit('close');
  };

  const formatDate = dt => new Date(dt).toLocaleString('pt-BR');

  watch(
    () => props.cardId,
    async newId => {
      if (newId) {
        await fetchCard();
        if (!modal && modalEl.value) modal = new Modal(modalEl.value);
        if (modal) modal.show();
      }
    }
  );

  onMounted(async () => {
    await nextTick();

    if (modalEl.value) {
      modal = new Modal(modalEl.value);
      modalEl.value.addEventListener('hidden.bs.modal', () => emit('close'));
    }

    if (props.cardId) {
      await fetchCard(); // 🔄 primeiro carrega o card
      loadingCard.value = false; // ✅ só então remove o preloader

      if (modal) modal.show();
    }
  });

  // Lightbox
  const getExtension = filename => {
    const parts = filename.split('.');
    return parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
  };

  const openPreview = att => {
    const images = card.value.attachments.filter(a => a.mime_type.startsWith('image'));
    imgsRef.value = images.map(img => `/storage/${img.path}`);
    indexRef.value = images.findIndex(img => img.id === att.id);
    visibleRef.value = true;
  };

  // Bloco Comentários ********************************************
  const addComment = async () => {
    // ADICIONE esta validação para HTML vazio
    if (!newComment.value.trim() || newComment.value === '<p><br></p>') return;

    try {
      const { data } = await axios.post(`/api/cards/${card.value.id}/comments`, {
        content: newComment.value,
      });

      console.log('Comentário adicionado:', data);

      // Garantir que comments seja um array antes de adicionar
      if (!card.value.comments) {
        card.value.comments = [];
      }

      // Adicionar o novo comentário
      card.value.comments.push(data);
      card.value.comments_count = card.value.comments.length; // ATUALIZA QTD
      newComment.value = '';

      // ADICIONE: Fecha o editor após comentar
      showCommentEditor.value = false;

      // Atualizar também a cópia local
      localCard.value.comments = [...card.value.comments];
      emit('update-card', card.value); // <-- notifica Kanban

      console.log('Estado atual dos comentários após adição:', card.value.comments);
    } catch (err) {
      console.error('Erro ao adicionar comentário:', err);
    }
  };

  const startEditComment = comment => {
    editingCommentId.value = comment.id;
    editedComment.value = comment.content;
  };

  const cancelEditComment = () => {
    editingCommentId.value = null;
    editedComment.value = '';
  };

  const saveEditedComment = async id => {
    // ADICIONE esta validação
    if (!editedComment.value.trim() || editedComment.value === '<p><br></p>') return;

    try {
      const { data } = await axios.put(`/api/comments/${id}`, {
        content: editedComment.value,
      });

      console.log('Resposta da edição:', data);

      // Encontrar o comentário a ser atualizado
      const index = card.value.comments.findIndex(c => c.id === id);

      if (index !== -1) {
        // Preservar o objeto user do comentário original
        const originalUser = card.value.comments[index].user;

        // Se o comentário retornado não tiver user, manter o original
        if (!data.user && originalUser) {
          data.user = originalUser;
        }

        // Certificar-se de que user existe
        if (!data.user) {
          data.user = { name: 'Usuário' }; // Fallback para evitar erros
        }

        // Atualizar o comentário na lista
        card.value.comments[index] = data;

        console.log('Comentário atualizado:', card.value.comments[index]);
      }

      // Limpar estados de edição
      editingCommentId.value = null;
      editedComment.value = '';
    } catch (err) {
      console.error('Erro ao salvar edição:', err);
    }
  };

  const confirmDeleteComment = id => {
    confirmingCommentId.value = id;
  };

  const cancelDeleteComment = () => {
    confirmingCommentId.value = null;
  };

  const deleteComment = async id => {
    try {
      await axios.delete(`/api/comments/${id}`);
      card.value.comments = card.value.comments.filter(c => c.id !== id);
      card.value.comments_count = card.value.comments.length; // atualiza quantidade
      confirmingCommentId.value = null; // limpa o popover de confirmação
      emit('update-card', card.value); // atualiza o Kanban
    } catch (err) {
      console.error('Erro ao excluir comentário:', err);
    }
  };

  const sortedComments = computed(() => {
    if (!card.value || !card.value.comments) return [];

    // Cria uma cópia para não modificar o array original durante a ordenação
    return [...card.value.comments].sort((a, b) => {
      // Converte strings de data para objetos Date para comparação
      const dateA = new Date(a.created_at);
      const dateB = new Date(b.created_at);

      // Ordena do mais recente para o mais antigo
      return dateB - dateA;
    });
  });
  const openCommentEditor = () => {
    showCommentEditor.value = true;
    // Foca no editor após abrir
    nextTick(() => {
      if (commentEditorRef.value && commentEditorRef.value.quill) {
        commentEditorRef.value.quill.focus();
      }
    });
  };

  const closeCommentEditor = () => {
    showCommentEditor.value = false;
    newComment.value = ''; // Limpa o conteúdo
  };

  // Links nos comentários
  const processCommentContent = content => {
    if (!content) return '';

    let html = content;
    const urlRegex =
      /(?<![">])((https?:\/\/)|(www\.))[a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=%]+(?<![.,;:])/gi;

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;

    const processTextNodes = node => {
      if (node.nodeType === Node.TEXT_NODE) {
        const text = node.textContent;
        const newHtml = text.replace(urlRegex, match => {
          const href = match.startsWith('http') ? match : 'https://' + match;
          return `<a href="${href}" target="_blank">${match}</a>`;
        });

        if (newHtml !== text) {
          const span = document.createElement('span');
          span.innerHTML = newHtml;
          node.parentNode.replaceChild(span, node);
          while (span.firstChild) {
            span.parentNode.insertBefore(span.firstChild, span);
          }
          span.parentNode.removeChild(span);
        }
      } else if (node.nodeType === Node.ELEMENT_NODE && node.tagName !== 'A') {
        const children = Array.from(node.childNodes);
        children.forEach(child => processTextNodes(child));
      }
    };

    processTextNodes(tempDiv);
    return tempDiv.innerHTML;
  };
  // FIM Bloco Comentários ********************************************

  // Bloco Anexo por Arraste e Solva ********************************************
  const handleDragOver = e => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
    draggingOver.value = true;
  };

  const handleDragLeave = e => {
    e.preventDefault();

    // Verifica se o cursor realmente saiu do modal
    const bounds = e.currentTarget.getBoundingClientRect();
    const isOutside =
      e.clientX < bounds.left ||
      e.clientX > bounds.right ||
      e.clientY < bounds.top ||
      e.clientY > bounds.bottom;

    if (isOutside) {
      draggingOver.value = false;
    }
  };

  const handleDrop = async e => {
    e.preventDefault();
    e.stopPropagation(); // impede que o drop suba para outro handler

    draggingOver.value = false;

    const files = e.dataTransfer.files;
    if (!files.length) return;

    const fakeEvent = { target: { files } };
    await uploadAttachments(fakeEvent);
  };

  function abrirSeletor() {
    if (fileInput.value) {
      console.log('Disparando clique no input');
      fileInput.value.click();
    } else {
      console.warn('fileInput.value está nulo');
    }
  }

  // Fim Bloco Anexo por Arraste e Solva ********************************************

  // Bloco de datas ********************************************
  function toggleDatePanel() {
    showDatePanel.value = !showDatePanel.value;
  }

  function handleDateUpdate(updatedCard) {
    card.value = updatedCard;
    emit('update-card', updatedCard);
    showDatePanel.value = false;
  }
  const isOverdue = dateStr => {
    const now = new Date();
    const date = new Date(dateStr);
    return date < now;
  };
  const handleCardUpdate = updatedCard => {
    card.value = { ...card.value, ...updatedCard };
    localCard.value = { ...localCard.value, ...updatedCard };
  };
  // Fim Bloco de datas ********************************************

  // Editor *****************************************************
  // Função para copiar um link e manter como link

  const formattedDescription = computed(() => {
    if (!card.value?.full_description)
      return '<i class="opacity-20 cor_icone small">Clique para adicionar uma descrição...</i>';

    let html = card.value.full_description;

    // Regex para detectar URLs que não estão dentro de tags <a>
    const urlRegex = /(?<!href="|href=')((https?:\/\/)|(www\.))[^\s<]+/gi;

    // Função para verificar se a URL já está dentro de uma tag <a>
    const isInsideAnchor = (html, index) => {
      const beforeText = html.substring(0, index);
      const lastOpenTag = beforeText.lastIndexOf('<a');
      const lastCloseTag = beforeText.lastIndexOf('</a>');
      return lastOpenTag > lastCloseTag;
    };

    // Substituir URLs por links, mas apenas se não estiverem já linkadas
    let result = html;
    let match;
    const replacements = [];

    while ((match = urlRegex.exec(html)) !== null) {
      if (!isInsideAnchor(html, match.index)) {
        let url = match[0];
        let href = url.startsWith('http') ? url : 'https://' + url;
        replacements.push({
          start: match.index,
          end: match.index + url.length,
          replacement: `<a href="${href}" target="_blank">${url}</a>`,
        });
      }
    }

    // Aplicar substituições de trás para frente para não afetar os índices
    for (let i = replacements.length - 1; i >= 0; i--) {
      const r = replacements[i];
      result = result.slice(0, r.start) + r.replacement + result.slice(r.end);
    }

    return result;
  });
  // Fim Função para copiar um link e manter como link

  // Função Mostrar Mais/Menos
  // Computed para verificar se a descrição é longa
  const isLongDescription = computed(() => {
    if (!card.value?.full_description) return false;

    // Remove tags HTML para contar caracteres reais
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = card.value.full_description;
    const textContent = tempDiv.textContent || tempDiv.innerText || '';

    // Considera longo se tiver mais de 300 caracteres ou 3 linhas
    return textContent.length > 300 || (textContent.match(/\n/g) || []).length > 3;
  });

  // Método para alternar exibição
  const toggleDescription = () => {
    showFullDescription.value = !showFullDescription.value;
  };

  // Atualize o handleContentClick para não expandir ao clicar no botão
  const handleContentClick = event => {
    // Se clicar no botão de mostrar mais/menos, não faz nada
    if (event.target.closest('.btn-link')) {
      return;
    }

    // Verifica se o clique foi em um link
    if (event.target.tagName === 'A') {
      event.preventDefault();
      event.stopPropagation();

      const url = event.target.href;
      if (url) {
        window.open(url, '_blank', 'noopener,noreferrer');
      }
    } else {
      // Se não for link, entra no modo de edição
      startEdit('full_description');
    }
  };

  // Reset ao fechar o modal ou mudar de card
  watch(
    () => props.cardId,
    () => {
      showFullDescription.value = false;
      showCommentEditor.value = false;
      newComment.value = '';
    }
  );
  // Fim Função Mostrar Mais/Menos
  // Fim Editor *****************************************************

  // Função ETIQUETAS/LABELS *****************************************
  const toggleLabelsPanel = () => {
    showLabelsPanel.value = !showLabelsPanel.value;
  };

  // ADICIONE ESTE MÉTODO PARA ATUALIZAR AS ETIQUETAS
  const handleLabelsUpdate = labels => {
    if (card.value) {
      card.value.labels = labels;
      localCard.value.labels = [...labels];
      emit('update-card', card.value);
    }
  };
  // Fim Função ETIQUETAS/LABELS *****************************************
</script>

<style scoped>
  .cor_icone {
    color: #bfc6d9;
  }
  .cor-texto {
    color: #adb4cb;
  }
  .card-modal-wrapper {
    background-color: #eff1f6;
    border-radius: 1.5rem;
  }
  .card-sidebar {
    border-radius: 1.12rem;
    background-color: #ffffff;
  }

  .editable-field {
    cursor: pointer;
    background: #f9f9f9;
    transition: background 0.2s;
  }
  .editable-field:hover {
    background: #f1f1f1;
  }

  input:focus {
    outline: 0.05rem solid #bfc6d9;
    background-color: #bfc6d9;
    border-radius: 0.5rem;
  }
  .text-area-editor {
    background-color: #e4e7f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
  }

  .form-label {
    font-weight: 500;
    font-size: 0.9125rem;
    color: #bfc6d9;
  }

  .overlay-modal-anexo {
    border-radius: 0.5rem;
  }
  .popover-box {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1060;
  }
  .anexo-preview {
    width: 50px;
    height: 50px;
    background: #f5f5f5;
    border: 1px solid #ccc;
    flex-shrink: 0;
  }

  .anexo-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    cursor: pointer;
  }

  .anexo-ext {
    font-weight: bold;
    color: #555;
  }

  .fade-popover-enter-active,
  .fade-popover-leave-active {
    transition:
      opacity 0.5s ease,
      transform 0.5s ease;
  }

  .fade-popover-enter-from,
  .fade-popover-leave-to {
    opacity: 0;
    transform: translateY(-25px);
  }

  .fade-popover-enter-to,
  .fade-popover-leave-from {
    opacity: 1;
    transform: translateY(0);
  }
  .drop-zone {
    background-color: #f9f9f9;
    transition: background 0.3s;
  }
  .drop-zone:hover {
    background-color: #e9ecef;
    cursor: pointer;
  }
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }

  /* EFEITOS MODAL ****************************************************/
  .zoom-fade-enter-active,
  .zoom-fade-leave-active {
    transition: all 0.35s ease;
  }

  .zoom-fade-enter-from,
  .zoom-fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
  }

  .zoom-fade-enter-to,
  .zoom-fade-leave-from {
    opacity: 1;
    transform: scale(1);
  }
  .modal.fade {
    background-color: rgba(0, 0, 0, 0.4); /* fundo escurecido */
    backdrop-filter: blur(2px); /* efeito de desfoque moderno */
  }
  .spinner-border {
    width: 3rem;
    height: 3rem;
  }
  .spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 2px;
  }
  .custom-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #007bff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  /* FIM EFEITOS MODAL *****************************************************/

  /* COMENTÁRIOS **********************************************************/
  .comment-item {
    transition: background 0.2s ease;
    position: relative;
  }
  .comment-actions a {
    text-decoration: none;
    opacity: 0.7;
  }

  .comment-actions a:hover {
    opacity: 1;
    text-decoration: underline;
  }
  .comment-content {
    background-color: #e6e9f1;
  }
  .comment-content:hover {
    background-color: #edf0f6;
    color: #000000;
  }
  .comment-content:hover .btn-comments {
    background-color: #d6d6e3;
    padding: 0rem 0.5rem !important;
    color: #000000;
    font-weight: bold;
  }
  textarea.comment-placeholder {
    border: 1px solid #b9bfd3;
  }
  ::-webkit-input-placeholder {
    color: #b9bfd3;
    font-style: italic;
  }
  /* FIM COMENTÁRIOS **********************************************************/
</style>
<!--
OPÇÃO 1: Adicione uma tag <style> SEM scoped no final do seu componente
Isso garantirá que os estilos sejam aplicados globalmente
-->

<style>
  /* Estilos globais para forçar formatação do Quill sobre Bootstrap */
  .formatted-content strong,
  .formatted-content b {
    font-weight: 700 !important;
    font-weight: bold !important;
  }

  .formatted-content em,
  .formatted-content i {
    font-style: italic !important;
  }

  .formatted-content u {
    text-decoration: underline !important;
    text-decoration-line: underline !important;
  }

  .formatted-content s,
  .formatted-content strike,
  .formatted-content del {
    text-decoration: line-through !important;
    text-decoration-line: line-through !important;
  }

  /* Forçar exibição de listas */
  .formatted-content ul {
    display: block !important;
    list-style: disc !important;
    list-style-type: disc !important;
    list-style-position: outside !important;
    padding-left: 40px !important;
    margin: 10px 0 !important;
  }

  .formatted-content ol {
    display: block !important;
    list-style: decimal !important;
    list-style-type: decimal !important;
    list-style-position: outside !important;
    padding-left: 40px !important;
    margin: 10px 0 !important;
  }

  .formatted-content ul li,
  .formatted-content ol li {
    display: list-item !important;
    list-style: inherit !important;
    list-style-type: inherit !important;
    margin: 5px 0 !important;
    padding-left: 5px !important;
  }

  /* Garantir que o texto apareça */
  .formatted-content ul li::before {
    content: none !important; /* Remove qualquer pseudo-elemento do Bootstrap */
  }

  .formatted-content p {
    margin: 0 0 10px 0 !important;
    display: block !important;
  }

  .formatted-content p:last-child {
    margin-bottom: 0 !important;
  }

  /* Links */
  .formatted-content a {
    color: #000000 !important;
    text-decoration: underline !important;
    cursor: pointer !important;
  }

  .formatted-content a:hover {
    color: #000000 !important;
    font-weight: bold;
  }

  /* Blockquote */
  .formatted-content blockquote {
    display: block !important;
    border-left: 4px solid #dee2e6 !important;
    padding-left: 1rem !important;
    margin: 1rem 0 !important;
    color: #6c757d !important;
    font-style: italic !important;
  }

  /* Headers */
  .formatted-content h1,
  .formatted-content h2,
  .formatted-content h3 {
    font-weight: 700 !important;
    line-height: 1.2 !important;
    margin-top: 1rem !important;
    margin-bottom: 0.5rem !important;
  }

  .formatted-content h1 {
    font-size: 2rem !important;
  }
  .formatted-content h2 {
    font-size: 1.5rem !important;
  }
  .formatted-content h3 {
    font-size: 1.25rem !important;
  }

  /* Código */
  .formatted-content code {
    background-color: #f8f9fa !important;
    padding: 0.2rem 0.4rem !important;
    border-radius: 0.25rem !important;
    color: #d63384 !important;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New',
      monospace !important;
    font-size: 87.5% !important;
  }

  .formatted-content pre {
    display: block !important;
    background-color: #f8f9fa !important;
    padding: 1rem !important;
    border-radius: 0.375rem !important;
    overflow-x: auto !important;
    margin: 1rem 0 !important;
  }

  .formatted-content pre code {
    background-color: transparent !important;
    padding: 0 !important;
    color: inherit !important;
  }

  .formatted-content {
    line-height: 0.9 !important;
  }

  /* CSS Mostrar Mais/Menos */
  /* Estilos para o conteúdo colapsado */
  .collapsed-content {
    max-height: 150px;
    overflow: hidden;
    position: relative;
  }

  /* Gradiente para indicar que há mais conteúdo */
  .collapsed-content::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50px;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.9));
    pointer-events: none;
  }

  /* Transição suave */
  .text-area-editor {
    transition: max-height 0.3s ease-in-out;
  }

  /* Estilo do botão mostrar mais/menos */
  .btn-link {
    color: #6c757d;
    font-size: 14px;
    padding: 5px 15px;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    background-color: #f8f9fa;
    transition: all 0.2s ease;
  }

  .btn-link:hover {
    background-color: #e9ecef;
    color: #495057;
    transform: translateY(-1px);
  }

  .btn-link:focus {
    box-shadow: none;
  }

  /* Animação do ícone */
  .btn-link i {
    transition: transform 0.3s ease;
  }

  /* Ajuste para tema escuro se necessário */
  @media (prefers-color-scheme: dark) {
    .collapsed-content::after {
      background: linear-gradient(to bottom, transparent, rgba(196, 206, 224, 0.5));
    }

    .btn-link {
      background-color: transparent;
      color: #b4bdd1;
      font-size: 12px !important ;
    }

    .btn-link:hover {
      background-color: #4a5568;
      color: #e2e8f0;
    }
  }

  /* Upload área */
  .upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 7px;
    text-align: center;
    color: #b9bfd3;
    background: none;
    cursor: pointer;
    transition: all 0.3s;
  }
  .upload-area i,
  .upload-area small {
    color: #b9bfd3 !important;
    transition: all 0.3s;
  }

  .upload-area:hover {
    border-color: #6c757d;
    background: #f8f9fa;
  }
</style>
