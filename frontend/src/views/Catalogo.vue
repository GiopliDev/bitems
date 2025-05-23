<template>
  <div class="catalogo-layout">
    <div class="catalogo-main">
      <h2>Catalogo Oggetti</h2>
      <section class="catalogo-section">
        <h3 class="section-title hot">🔥 Hot Items</h3>
        <div class="catalogo-cards">
          <ItemCard v-for="item in hotItems" :key="item.id" :item="item" />
        </div>
      </section>
      <section class="catalogo-section">
        <h3 class="section-title recent">🕒 Recent Items</h3>
        <div class="catalogo-cards">
          <ItemCard v-for="item in recentItems" :key="item.id" :item="item" />
        </div>
      </section>
      <section class="catalogo-section" v-for="(items, game) in itemsByGame" :key="game">
        <h3 class="section-title">{{ game }}</h3>
        <div class="catalogo-cards">
          <ItemCard v-for="item in items" :key="item.id" :item="item" />
          <router-link 
            v-if="!route.query.game" 
            :to="{ path: '/catalogo', query: { game: game } }" 
            class="see-more"
          >
            Vedi tutti
          </router-link>
        </div>
      </section>
    </div>
    <FilterBar @filters-applied="handleFiltersApplied" />
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import ItemCard from '../components/ItemCard.vue'
import FilterBar from '../components/FilterBar.vue'
import axios from '@/config/axios'

const route = useRoute()
const catalogo = ref<Record<string, any[]>>({})
const hotItems = ref<any[]>([])
const recentItems = ref<any[]>([])
const itemsByGame = ref<Record<string, any[]>>({})

function handleFiltersApplied(filteredData: any[]) {
  // Aggiorna i dati visualizzati con i risultati filtrati
  itemsByGame.value = { 'Risultati Filtro': filteredData }
}

async function loadCatalogo() {
  try {
    if (route.query.game) {
      // Se c'è un game nell'URL, carica il catalogo filtrato
      const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
        params: {
          action: 'getCatalogoFiltrato',
          gameName: route.query.game
        }
      })
      itemsByGame.value = { [route.query.game as string]: response.data.data }
    } else {
      // Altrimenti carica il catalogo completo
      const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
        params: { action: 'getCatalogoDivisoInSezioni' }
      })
      catalogo.value = response.data.data
      itemsByGame.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading catalog:', error)
  }
}

// Carica il catalogo al mount e quando cambia il parametro game
onMounted(loadCatalogo)
watch(() => route.query.game, loadCatalogo)
</script>

<style scoped>
.catalogo-layout {
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 2rem 1rem;
  gap: 2rem;
}
.catalogo-main {
  flex: 1;
  background: #18181c;
  border-radius: 16px;
  padding: 2rem 2rem 2rem 2rem;
  min-width: 0;
  box-shadow: 0 2px 16px #0002;
}
.catalogo-section {
  margin-bottom: 2.5rem;
}
.section-title {
  color: var(--primary-light);
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 1rem;
  letter-spacing: 0.01em;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.section-title.hot {
  color: var(--secondary);
}
.section-title.recent {
  color: var(--primary);
}
.catalogo-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 0.5rem;
}
.see-more {
  display: inline-block;
  margin-left: 1rem;
  color: var(--primary-light);
  font-size: 1rem;
  text-decoration: underline;
  align-self: center;
  transition: color 0.2s;
}
.see-more:hover {
  color: var(--secondary);
}
h2 {
  color: var(--on-background);
  margin-bottom: 2.5rem;
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: 0.01em;
}
</style> 