<template>
  <teleport to="body">
    <div v-if="show" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <h5 class="mb-3">{{ title }}</h5>
            <p class="text-muted mb-4">{{ message }}</p>
            <div class="d-flex gap-2 justify-content-center">
              <button
                type="button"
                class="btn btn-label-secondary"
                @click="$emit('cancel')">
                Cancelar
              </button>
              <button
                type="button"
                :class="['btn', danger ? 'btn-danger' : 'btn-primary']"
                @click="$emit('confirm')">
                {{ confirmText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Confirmar'
  },
  danger: {
    type: Boolean,
    default: false
  }
});

defineEmits(['confirm', 'cancel']);
</script>
