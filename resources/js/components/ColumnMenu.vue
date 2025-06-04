<template>
  <div class="dropdown">
    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
      <i class="fa-solid fa-ellipsis-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li>
        <a class="dropdown-item" href="#" @click.prevent="showColorPicker = true">Cor</a>
      </li>
      <li>
        <a class="dropdown-item" href="#" @click.prevent="showSortMenu = true">Ordenar por</a>
      </li>
    </ul>

    <div
      v-if="showColorPicker"
      class="popover-body bg-white shadow rounded p-2 mt-1 position-absolute z-10"
    >
      <div class="d-flex flex-wrap gap-2">
        <button
          v-for="color in colors"
          :key="color"
          class="btn p-3 border rounded"
          :style="{ backgroundColor: color }"
          @click="changeColor(color)"
        ></button>
      </div>
    </div>

    <div
      v-if="showSortMenu"
      class="popover-body bg-white shadow rounded p-2 mt-1 position-absolute z-10"
    >
      <div class="list-group">
        <button class="list-group-item list-group-item-action" @click="sortCards('created_desc')">
          Data recente primeiro
        </button>
        <button class="list-group-item list-group-item-action" @click="sortCards('created_asc')">
          Data antiga primeiro
        </button>
        <button class="list-group-item list-group-item-action" @click="sortCards('title')">
          Ordem alfabética
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, watch } from 'vue';
  import axios from 'axios';

  const props = defineProps({
    column: Object,
  });
  const emit = defineEmits(['update:column']);

  const showColorPicker = ref(false);
  const showSortMenu = ref(false);

  const colors = [
    '#2122b1',
    '#3637e6',
    '#5f62f7',
    '#9b9cfb',
    '#d7d8fe',

    '#064a5a',
    '#0b6b84',
    '#159ec2',
    '#4fd8fe',
    '#b2efff',

    '#0a3473',
    '#185abc',
    '#3983ee',
    '#9bc3fe',
    '#d2e5fe',

    '#0b5754',
    '#1c806a',
    '#36b67f',
    '#85eaad',
    '#d9fbdd',

    '#784000',
    '#bb6d00',
    '#ffab01',
    '#ffd664',
    '#fff4cf',

    '#780707',
    '#ba1919',
    '#fe2c2c',
    '#ff7e7e',
    '#ffd4d4',

    '#780707',
    '#b51e16',
    '#fe582c',
    '#fead82',
    '#ffead3',
  ];

  const changeColor = async color => {
    try {
      await axios.put(`/api/columns/${props.column.id}/update-color`, { color });
      emit('update:column', { ...props.column, color });
      showColorPicker.value = false;
    } catch (err) {
      console.error('Erro ao mudar cor:', err);
    }
  };

  const sortCards = mode => {
    const sorted = [...props.column.cards];
    if (mode === 'created_desc') {
      sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (mode === 'created_asc') {
      sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    } else if (mode === 'title') {
      sorted.sort((a, b) => a.title.localeCompare(b.title));
    }

    const cardIds = sorted.map(c => c.id);
    axios
      .post('/api/cards/update-order', {
        cards: cardIds,
        column_id: props.column.id,
      })
      .then(() => {
        emit('update:column', { ...props.column, cards: sorted });
      });
    showSortMenu.value = false;
  };
</script>

<style scoped>
  .popover-body {
    width: 200px;
  }
</style>
