<template>
  <transition name="fade" mode="out-in">
    <router-view />
  </transition>
</template>

<script setup>
// Nenhum import necessário aqui
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
