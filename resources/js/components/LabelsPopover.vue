<template>
  <div class="labels-popover-container">
    <!-- Modal de Etiquetas -->
    <div class="labels-modal-backdrop" @click="close">
      <div class="labels-modal" @click.stop>
        <!-- Header do Modal -->
        <div class="labels-modal-header">
          <h5 class="mb-0">Etiquetas</h5>
          <button class="btn-close" @click="close"></button>
        </div>

        <!-- Busca -->
        <div class="labels-modal-search">
          <input
            v-model="labelSearchQuery"
            type="text"
            class="form-control"
            placeholder="Buscar etiquetas..."
          />
        </div>

        <!-- Lista de Etiquetas -->
        <div class="labels-modal-body">
          <div class="labels-section">
            <h6 class="text-muted small mb-2">Etiquetas</h6>

            <div
              v-for="label in filteredLabels"
              :key="label.id"
              class="label-item"
              @click="toggleLabel(label)"
            >
              <input
                type="checkbox"
                :checked="isLabelSelected(label)"
                class="form-check-input me-2"
              />
              <div class="label-color-box" :style="{ backgroundColor: label.color }">
                <span class="label-name">{{ label.name || 'Sem nome' }}</span>
              </div>
              <button class="btn btn-sm btn-ghost ms-auto" @click.stop="editLabel(label)">
                <i class="fas fa-pencil"></i>
              </button>
            </div>

            <!-- Criar nova etiqueta -->
            <button class="btn btn-light w-100 mt-3" @click="showCreateLabel = true">
              Criar uma nova etiqueta
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="labels-modal-footer">
          <small class="text-muted">Habilitar o modo compatível para usuários com daltonismo</small>
        </div>
      </div>
    </div>

    <!-- Modal de Criar/Editar Etiqueta -->
    <div v-if="showCreateLabel" class="create-label-backdrop" @click="closeCreateLabel">
      <div class="create-label-modal" @click.stop>
        <h6 class="mb-3">{{ editingLabel ? 'Editar' : 'Criar' }} etiqueta</h6>

        <!-- Nome da etiqueta -->
        <input
          v-model="newLabelName"
          type="text"
          class="form-control mb-3"
          placeholder="Nome da etiqueta"
          @keyup.enter="saveLabel"
          ref="labelNameInput"
        />

        <!-- Grid de cores -->
        <div class="color-grid mb-3">
          <div
            v-for="color in availableColors"
            :key="color"
            class="color-option"
            :class="{ selected: newLabelColor === color }"
            :style="{ backgroundColor: color }"
            @click="newLabelColor = color"
          >
            <i v-if="newLabelColor === color" class="fas fa-check"></i>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-secondary btn-sm" @click="closeCreateLabel">Cancelar</button>
          <button
            class="btn btn-primary btn-sm"
            @click="saveLabel"
            :disabled="!newLabelName.trim()"
          >
            {{ editingLabel ? 'Salvar' : 'Criar' }}
          </button>
          <button v-if="editingLabel" class="btn btn-danger btn-sm" @click="deleteLabel">
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, watch, nextTick } from 'vue';
  import axios from 'axios';

  const props = defineProps({
    card: {
      type: Object,
      required: true,
    },
  });

  const emit = defineEmits(['close', 'updated']);

  // Estado do componente
  const showCreateLabel = ref(false);
  const labelSearchQuery = ref('');
  const selectedLabels = ref([]);
  const editingLabel = ref(null);
  const newLabelName = ref('');
  const newLabelColor = ref('#FF6B6B');
  const labelNameInput = ref(null);

  // Lista de etiquetas disponíveis
  const availableLabels = ref([]);

  // Cores disponíveis para etiquetas
  const availableColors = [
    '#FF6B6B',
    '#4ECDC4',
    '#45B7D1',
    '#96CEB4',
    '#FECA57',
    '#F7B731',
    '#5F27CD',
    '#00D2D3',
    '#FF9FF3',
    '#54A0FF',
    '#48DBFB',
    '#A29BFE',
    '#FD79A8',
    '#BADC58',
    '#F8B500',
    '#FF6348',
    '#7BED9F',
    '#70A1FF',
    '#DFE4EA',
    '#57606F',
  ];

  // Computed
  const filteredLabels = computed(() => {
    if (!labelSearchQuery.value) return availableLabels.value;

    return availableLabels.value.filter(label =>
      label.name.toLowerCase().includes(labelSearchQuery.value.toLowerCase())
    );
  });

  // Métodos
  const close = () => {
    emit('close');
  };

  const toggleLabel = async label => {
    console.log('Toggle label:', label.name);
    console.log('Labels antes:', [...selectedLabels.value]);

    const index = selectedLabels.value.findIndex(l => l.id === label.id);
    if (index > -1) {
      selectedLabels.value.splice(index, 1);
    } else {
      selectedLabels.value.push(label);
    }

    console.log('Labels depois:', [...selectedLabels.value]);

    await updateCardLabels();
  };

  const isLabelSelected = label => {
    const isSelected = selectedLabels.value.some(l => l.id === label.id);
    console.log(`Label ${label.name} selecionada:`, isSelected); // DEBUG
    return isSelected;
  };

  const editLabel = label => {
    editingLabel.value = label;
    newLabelName.value = label.name;
    newLabelColor.value = label.color;
    showCreateLabel.value = true;

    nextTick(() => {
      labelNameInput.value?.focus();
    });
  };

  const saveLabel = async () => {
    if (!newLabelName.value.trim()) return;

    try {
      if (editingLabel.value) {
        // Editar etiqueta existente
        const { data } = await axios.put(`/api/labels/${editingLabel.value.id}`, {
          name: newLabelName.value,
          color: newLabelColor.value,
        });

        const index = availableLabels.value.findIndex(l => l.id === editingLabel.value.id);
        if (index > -1) {
          availableLabels.value[index] = data;
        }

        // Atualizar nas selecionadas também
        const selectedIndex = selectedLabels.value.findIndex(l => l.id === editingLabel.value.id);
        if (selectedIndex > -1) {
          selectedLabels.value[selectedIndex] = data;
        }
      } else {
        // Criar nova etiqueta
        const { data } = await axios.post('/api/labels', {
          name: newLabelName.value,
          color: newLabelColor.value,
        });

        availableLabels.value.push(data);
      }

      closeCreateLabel();
    } catch (error) {
      console.error('Erro ao salvar etiqueta:', error);
      // Aqui você pode adicionar um toast de erro
    }
  };

  const deleteLabel = async () => {
    if (!editingLabel.value) return;

    try {
      await axios.delete(`/api/labels/${editingLabel.value.id}`);

      const index = availableLabels.value.findIndex(l => l.id === editingLabel.value.id);
      if (index > -1) {
        availableLabels.value.splice(index, 1);
      }

      // Remover das selecionadas também
      selectedLabels.value = selectedLabels.value.filter(l => l.id !== editingLabel.value.id);

      closeCreateLabel();
      await updateCardLabels();
    } catch (error) {
      console.error('Erro ao deletar etiqueta:', error);
    }
  };

  const closeCreateLabel = () => {
    showCreateLabel.value = false;
    editingLabel.value = null;
    newLabelName.value = '';
    newLabelColor.value = '#FF6B6B';
  };

  const updateCardLabels = async () => {
    try {
      const labelIds = selectedLabels.value.map(l => l.id);

      console.log('=== ATUALIZANDO LABELS ===');
      console.log('Labels selecionadas:', selectedLabels.value);
      console.log('IDs sendo enviados:', labelIds);

      const { data } = await axios.put(`/api/cards/${props.card.id}/labels`, {
        label_ids: labelIds,
      });

      console.log('Resposta do servidor:', data);
      console.log('Labels retornadas:', data.labels);

      // Emite as labels retornadas pelo servidor
      emit('updated', data.labels || []);
    } catch (error) {
      console.error('Erro ao atualizar etiquetas:', error);
      if (error.response) {
        console.error('Resposta de erro:', error.response.data);
      }
    }
  };

  const fetchLabels = async () => {
    try {
      const { data } = await axios.get('/api/labels');
      availableLabels.value = data;
    } catch (error) {
      console.error('Erro ao buscar etiquetas:', error);
      // Dados de exemplo caso a API não esteja pronta
      availableLabels.value = [
        { id: 1, name: 'Urgente', color: '#FF6B6B' },
        { id: 2, name: 'Importante', color: '#4ECDC4' },
        { id: 3, name: 'Revisão', color: '#45B7D1' },
        { id: 4, name: 'Bug', color: '#F7B731' },
        { id: 5, name: 'Feature', color: '#5F27CD' },
        { id: 6, name: 'Documentação', color: '#00D2D3' },
      ];
    }
  };

  // Lifecycle
  // No LabelsPopover.vue, modifique o onMounted
  onMounted(async () => {
    await fetchLabels();

    if (props.card && props.card.labels) {
      console.log('Props card completo:', props.card);
      console.log('Labels do card:', props.card.labels);

      // Simplifica - assumindo que sempre vem objetos completos
      selectedLabels.value = [...props.card.labels];

      console.log('Labels selecionadas inicialmente:', selectedLabels.value);
    }
  });

  // Watchers
  watch(
    () => showCreateLabel.value,
    newValue => {
      if (newValue) {
        nextTick(() => {
          labelNameInput.value?.focus();
        });
      }
    }
  );

  watch(
    () => props.card,
    newCard => {
      if (newCard && newCard.labels) {
        selectedLabels.value = [...newCard.labels];
      }
    },
    { deep: true, immediate: true }
  );

  // Adicione este watcher para garantir sincronização
  watch(
    () => props.card?.labels,
    newLabels => {
      console.log('Labels do card mudaram (watcher):', newLabels);
      if (newLabels) {
        selectedLabels.value = [...newLabels];
      }
    },
    { immediate: true, deep: true }
  );
</script>

<style scoped>
  /* Modal de etiquetas */
  .labels-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 23px;
    z-index: 1060;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .labels-modal {
    background: white;
    border-radius: 8px;
    width: 400px;
    max-height: 600px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  }

  .labels-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .labels-modal-search {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
  }

  .labels-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px 20px;
  }

  .labels-modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e9ecef;
    text-align: center;
  }

  /* Itens de etiqueta */
  .label-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    cursor: pointer;
    border-radius: 4px;
    transition: background 0.2s;
  }

  .label-item:hover {
    background: #f8f9fa;
  }

  .label-color-box {
    flex: 1;
    padding: 8px 12px;
    border-radius: 4px;
    color: white;
    font-weight: 500;
    margin: 0 8px;
  }

  .label-name {
    filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.2));
  }

  .btn-ghost {
    background: transparent;
    border: none;
    color: #6c757d;
    opacity: 0;
    transition: opacity 0.2s;
  }

  .label-item:hover .btn-ghost {
    opacity: 1;
  }

  /* Modal de criar etiqueta */
  .create-label-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 23px;
    z-index: 1070;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .create-label-modal {
    background: white;
    border-radius: 8px;
    padding: 20px;
    width: 320px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  }

  /* Grid de cores */
  .color-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
  }

  .color-option {
    width: 48px;
    height: 36px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
    color: white;
  }

  .color-option:hover {
    transform: scale(1.1);
  }

  .color-option.selected {
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.2);
  }

  /* Tema escuro */
  @media (prefers-color-scheme: dark) {
    .labels-modal,
    .create-label-modal {
      background: #eff1f6;
      color: #000;
    }

    .labels-modal-header,
    .labels-modal-search,
    .labels-modal-footer {
      border-color: #e4e7f0;
    }

    .label-item:hover {
      background: #e4e7f0;
    }

    .form-control {
      background: #e4e7f0;
      border: 0;
      color: #000000;
    }
  }
</style>
