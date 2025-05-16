<template>
  <div class="popup-overlay" @click.self="close">
    <div class="popup-card">
      <button class="close-btn" @click="close">&times;</button>
      <h2>Pagamento</h2>
      <p>Questa è una simulazione di pagamento.<br>i soldi verranno prelevati dal saldo del sito</p>
      <div class="popup-actions">
        <button class="main-btn" @click="confirm">Conferma pagamento</button>
        <button class="main-btn outline" @click="close">Annulla</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close'])
function close() { emit('close') }
function confirm() {
  alert('Pagamento simulato!')
  emit('close')
}
</script>

<style scoped>
.popup-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.6);
  display: flex; align-items: center; justify-content: center; z-index: 1000;
}
.popup-card {
  background: radial-gradient(ellipse at top left, #1e2a2f 0%, #18181c 100%);
  border: 2px solid var(--secondary, #03dac6);
  border-radius: 20px;
  padding: 2.5rem 2rem 1.5rem 2rem;
  min-width: 340px;
  max-width: 95vw;
  box-shadow: 0 8px 32px #000a;
  position: relative;
  color: var(--on-surface, #fff);
  display: flex;
  flex-direction: column;
  align-items: center;
}
.close-btn {
  position: absolute; top: 12px; right: 18px;
  background: none; border: none; color: #bbb;
  font-size: 2rem; cursor: pointer; transition: color 0.2s;
}
.close-btn:hover { color: var(--error, #cf6679); }
.popup-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}
.main-btn {
  padding: 0.7rem 1.5rem;
  border: 2px solid var(--primary-light, #bb86fc);
  background: var(--primary-light, #bb86fc);
  color: var(--on-primary, #fff);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.main-btn.outline {
  background: transparent;
  color: var(--primary-light, #bb86fc);
  border: 2px solid var(--primary-light, #bb86fc);
}
.main-btn.outline:hover {
  background: #222;
  color: var(--secondary, #03dac6);
}
</style> 