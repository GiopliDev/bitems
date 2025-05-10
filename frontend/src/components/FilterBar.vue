<template>
  <aside class="filter-bar">
    <h3>Filtri:</h3>
    <div class="filter-group">
      <label>Gioco:</label>
      <input type="text" v-model="filters.game" />
    </div>
    <div class="filter-group">
      <label>Categoria Oggetto:</label>
      <input type="text" v-model="filters.category" />
    </div>
    <div class="filter-group">
      <label>Prezzo Minimo:</label>
      <input type="number" v-model="filters.minPrice" />
    </div>
    <div class="filter-group">
      <label>Prezzo Massimo:</label>
      <input type="number" v-model="filters.maxPrice" />
    </div>
    <button class="main-btn" @click="apply">Applica e Cerca</button>
    <button class="main-btn outline" @click="reset">Cancella filtri</button>
  </aside>
</template>

<script setup>
import { reactive } from 'vue'
const filters = reactive({
  game: '',
  category: '',
  minPrice: '',
  maxPrice: ''
})
function apply() {
  // In futuro: emetti evento con i filtri
  alert('Applica filtri (demo)')
}
function reset() {
  filters.game = ''
  filters.category = ''
  filters.minPrice = ''
  filters.maxPrice = ''
}
</script>

<style scoped>
.filter-bar {
  background: #18181c;
  color: var(--secondary, #03dac6);
  border-radius: 10px;
  padding: 2rem 1.5rem;
  min-width: 220px;
  max-width: 270px;
  margin-left: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  font-size: 1.1rem;
  border: 1.5px solid var(--secondary, #03dac6);
}
.filter-bar h3 {
  color: var(--secondary, #03dac6);
  margin-bottom: 1rem;
}
.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
label {
  color: var(--secondary, #03dac6);
  font-size: 1rem;
}
input {
  background: transparent;
  border: 1.5px solid var(--secondary, #03dac6);
  border-radius: 6px;
  padding: 0.4rem 0.7rem;
  color: #fff;
  font-size: 1rem;
  margin-bottom: 0.2rem;
}
input:focus { border-color: var(--primary-light, #bb86fc); }
.main-btn {
  margin-top: 0.7rem;
  padding: 0.5rem 1.2rem;
  border: 2px solid var(--secondary, #03dac6);
  background: transparent;
  color: var(--secondary, #03dac6);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.main-btn:hover {
  background: var(--secondary, #03dac6);
  color: #18181c;
}
.main-btn.outline {
  background: transparent;
  color: var(--secondary, #03dac6);
  border: 2px solid var(--secondary, #03dac6);
}
.main-btn.outline:hover {
  background: #222;
  color: var(--primary-light, #bb86fc);
}
</style> 