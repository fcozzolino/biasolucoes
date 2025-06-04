<template>
  <div
    ref="popoverRef"
    class="date-popover shadow-lg border rounded bg-white p-3"
    style="width: 300px"
  >
    <h6 class="mb-3">Datas</h6>

    <!-- Data de Início -->
    <div class="form-check mb-2">
      <input type="checkbox" class="form-check-input" v-model="hasStartDate" id="startDateToggle" />
      <label class="form-check-label" for="startDateToggle">Data de início</label>
    </div>
    <input
      v-if="hasStartDate"
      type="datetime-local"
      class="form-control mb-3"
      v-model="form.start_date"
    />

    <!-- Data de Entrega -->
    <div class="form-check mb-2">
      <input type="checkbox" class="form-check-input" v-model="hasDueDate" id="dueDateToggle" />
      <label class="form-check-label" for="dueDateToggle">Data de entrega</label>
    </div>
    <input
      v-if="hasDueDate"
      type="datetime-local"
      class="form-control mb-3"
      v-model="form.due_date"
    />

    <!-- Lembrete -->
    <div class="mb-3">
      <label class="form-label">Definir lembrete</label>
      <select class="form-select" v-model="form.reminder_interval">
        <option value="" disabled>Selecione...</option>
        <option value="1 day">1 dia antes</option>
        <option value="2 days">2 dias antes</option>
        <option value="1 hour">1 hora antes</option>
      </select>
    </div>

    <div class="d-flex justify-content-between">
      <button class="btn btn-outline-danger" @click="removeDates">Remover</button>
      <button class="btn btn-primary" @click="saveDates">Salvar</button>
    </div>
  </div>
</template>

<script setup>
  import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
  import axios from 'axios';

  const props = defineProps({
    card: { type: Object, required: true },
  });

  const emit = defineEmits(['close', 'updated']);

  const hasStartDate = ref(false);
  const hasDueDate = ref(false);

  const form = reactive({
    start_date: '',
    due_date: '',
    reminder_interval: '',
  });

  const popoverRef = ref(null);

  // Fecha o popover se clicar fora
  const handleClickOutside = event => {
    if (popoverRef.value && !popoverRef.value.contains(event.target)) {
      emit('close');
    }
  };

  onMounted(() => {
    if (props.card.start_date) {
      form.start_date = props.card.start_date.slice(0, 16);
      hasStartDate.value = true;
    }

    if (props.card.due_date) {
      form.due_date = props.card.due_date.slice(0, 16);
      hasDueDate.value = true;
    }

    if (props.card.reminder_interval) {
      form.reminder_interval = props.card.reminder_interval;
    }

    document.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
  });

  const saveDates = async () => {
    try {
      const payload = {
        start_date: hasStartDate.value ? form.start_date : null,
        due_date: hasDueDate.value ? form.due_date : null,
        reminder_interval: form.reminder_interval,
      };

      const { data } = await axios.put(`/api/cards/${props.card.id}`, payload);
      const full = await axios.get(`/api/cards/${props.card.id}`);
      emit('updated', full.data);
    } catch (err) {
      console.error('Erro ao salvar datas:', err);
    }
  };

  const removeDates = async () => {
    try {
      const payload = {
        start_date: null,
        due_date: null,
        reminder_interval: null,
      };

      const { data } = await axios.put(`/api/cards/${props.card.id}`, payload);

      // Recarrega os dados completos do card após a remoção
      const full = await axios.get(`/api/cards/${props.card.id}`);
      emit('updated', full.data);
    } catch (err) {
      console.error('Erro ao remover datas:', err);
    }
  };
</script>

<style scoped>
  .date-popover {
    position: absolute;
    top: 20px;
    right: 0;
    z-index: 999;
    background: #fff;
    border-radius: 0.5rem;
  }
</style>
