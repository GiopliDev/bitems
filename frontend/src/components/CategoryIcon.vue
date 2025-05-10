<template>
  <div
    class="game-icon-emoji"
    @mouseenter="hover = true"
    @mouseleave="hover = false"
  >
    <router-link :to="to" class="emoji-link">
      <span class="emoji" :style="{ color }">{{ emoji }}</span>
      <span class="emoji-label" v-show="hover">{{ label }}</span>
    </router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const props = defineProps({
  emoji: String,
  color: String,
  label: String,
  to: [String, Object]
})
const hover = ref(false)
</script>

<style scoped>
.game-icon-emoji {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
  background: var(--surface-light, #222);
  border-radius: 12px;
  aspect-ratio: 1;
  min-width: 80px;
  min-height: 80px;
  border: 2px solid rgba(255,255,255,0.12);
  box-shadow: 0 2px 8px rgba(0,0,0,0.10);
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}
.game-icon-emoji:hover {
  transform: scale(1.13);
  box-shadow: 0 6px 24px rgba(187, 134, 252, 0.18);
  border-color: var(--primary-light);
  z-index: 2;
}

.emoji-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
}

.emoji {
  font-size: 3rem;
  filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));
  transition: filter 0.2s;
  cursor: pointer;
}
.emoji-link:hover .emoji {
  filter: drop-shadow(0 4px 16px rgba(0,0,0,0.35));
}

.emoji-label {
  position: absolute;
  top: -2.2rem;
  left: 50%;
  transform: translateX(-50%);
  background: var(--surface-light, #222);
  color: var(--on-surface, #fff);
  padding: 0.3rem 0.8rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  opacity: 0;
  pointer-events: none;
  white-space: nowrap;
  transition: opacity 0.2s, top 0.2s;
  z-index: 2;
}
.emoji-link:hover .emoji-label {
  opacity: 1;
  top: -3.2rem;
}
</style> 