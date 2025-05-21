<template>
  <div class="image-popup" v-if="show" @click="close">
    <div class="image-container" @click.stop>
      <img :src="imageUrl" alt="Full size image" class="full-image" />
      <button class="close-btn" @click="close">×</button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  show: boolean
  imageUrl: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

function close() {
  emit('close')
}
</script>

<style scoped>
.image-popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.image-container {
  position: relative;
  max-width: 90%;
  max-height: 90%;
}

.full-image {
  max-width: 100%;
  max-height: 90vh;
  object-fit: contain;
}

.close-btn {
  position: absolute;
  top: -40px;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
  padding: 0.5rem;
  transition: transform 0.2s;
}

.close-btn:hover {
  transform: scale(1.1);
}
</style> 