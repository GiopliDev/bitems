<template>
  <aside class="filter-bar">
    <h3>Filtra oggetti</h3>
    <div class="filter-group">
      <label for="game">Gioco:</label>
      <select id="game" v-model="filters.game">
        <option value="">Tutti</option>
        <option v-for="game in games" :key="game" :value="game">{{ game }}</option>
      </select>
    </div>
    <div class="filter-group">
      <label for="category">Categoria:</label>
      <select id="category" v-model="filters.category">
        <option value="">Tutte</option>
        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
      </select>
    </div>
    <div class="filter-group">
      <label>Prezzo:</label>
      <div class="price-inputs">
        <input type="number" min="0" max="2000" step="1" v-model.number="filters.minPrice" placeholder="Min €" />
        <span class="price-sep">-</span>
        <input type="number" min="0" max="2000" step="1" v-model.number="filters.maxPrice" placeholder="Max €" />
      </div>
    </div>
    <button class="main-btn" @click="apply">Applica e Cerca</button>
    <button class="main-btn outline" @click="reset">Cancella filtri</button>
  </aside>
</template>

<script setup>
import { reactive, ref } from 'vue'
const filters = reactive({
  game: '',
  category: '',
  minPrice: 0,
  maxPrice: 2000
})
const games = [
  'Valorant',
  'League of Legends',
  'CS:GO'
]
const categories = [
  'Cosmetica',
  'Ingame Items',
  'Boosting',
  'Account',
  'Skin'
]
function apply() {
  // In futuro: emetti evento con i filtri
  alert('Applica filtri (demo)')
}
function reset() {
  filters.game = ''
  filters.category = ''
  filters.minPrice = 0
  filters.maxPrice = 2000
}
</script>

<style scoped>
.filter-bar {
  position: sticky;
  top: 0;
  height: 100vh;
  background: var(--surface);
  color: var(--on-surface);
  border-radius: 18px 0 0 18px;
  padding: 2.5rem 2rem 2rem 2rem;
  min-width: 270px;
  max-width: 320px;
  box-shadow: 0 4px 32px #0003;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  font-size: 1.1rem;
  border-left: 4px solid var(--primary-light);
  z-index: 10;
}
.filter-bar h3 {
  color: var(--primary-light);
  margin-bottom: 1.5rem;
  font-size: 1.3rem;
  font-weight: 700;
  letter-spacing: 0.01em;
}
.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
label {
  color: var(--primary-light);
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
}
select, input[type="number"] {
  background: var(--surface-light);
  border: 1.5px solid var(--primary-light);
  border-radius: 8px;
  padding: 0.5rem 0.8rem;
  color: var(--on-surface);
  font-size: 1rem;
  margin-bottom: 0.2rem;
  outline: none;
  transition: border-color 0.2s;
}
select:focus, input[type="number"]:focus {
  border-color: var(--secondary);
}
.price-inputs {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}
.price-sep {
  color: var(--primary-light);
  font-weight: 700;
  font-size: 1.2rem;
}
.main-btn {
  margin-top: 0.7rem;
  padding: 0.7rem 1.5rem;
  border: 2px solid var(--primary-light);
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.main-btn:hover {
  background: var(--secondary);
  color: #18181c;
}
.main-btn.outline {
  background: transparent;
  color: var(--primary-light);
  border: 2px solid var(--primary-light);
}
.main-btn.outline:hover {
  background: #222;
  color: var(--secondary);
}
</style> 