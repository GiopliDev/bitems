<template>
  <div class="catalogo-layout">
    <div class="catalogo-main">
      <h2>Catalogo Oggetti</h2>
      <div v-if="loading" class="loading">
        Caricamento catalogo...
      </div>
      <div v-else-if="error" class="error">
        {{ error }}
      </div>
      <template v-else>
        <section class="catalogo-section" v-for="(items, game) in itemsByGame" :key="game">
          <h3 class="section-title">{{ game }}</h3>
          <div class="catalogo-cards">
            <ItemCard 
              v-for="item in items" 
              :key="item.art_id" 
              :item="item"
            />
            <router-link 
              v-if="!route.query.game" 
              :to="{ path: '/catalogo', query: { game: game } }" 
              class="see-more"
            >
              Vedi tutti
            </router-link>
          </div>
        </section>
      </template>
    </div>
    <FilterBar @filters-applied="handleFiltersApplied" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ItemCard from '../components/ItemCard.vue'
import FilterBar from '../components/FilterBar.vue'
import axios from '@/config/axios'

const route = useRoute()
const router = useRouter()
const itemsByGame = ref<Record<string, any[]>>({})
const loading = ref(true)
const error = ref('')

async function loadCatalogo() {
  loading.value = true
  error.value = ''
  
  try {
    if (route.query.game) {
      // Se c'è un game nell'URL, carica il catalogo filtrato
      const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
        params: {
          action: 'getCatalogoFiltrato',
          gameName: route.query.game
        }
      })
      
      if (response.data.success) {
        itemsByGame.value = { [route.query.game as string]: response.data.data }
      } else {
        throw new Error('Errore nel caricamento del catalogo filtrato')
      }
    } else {
      // Altrimenti carica il catalogo completo
      const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
        params: { action: 'getCatalogoDivisoInSezioni' }
      })
      
      console.log('Catalogo response:', response.data) // Debug log
      
      if (response.data.success) {
        itemsByGame.value = response.data.data
        console.log('Items by game:', itemsByGame.value) // Debug log
      } else {
        throw new Error('Errore nel caricamento del catalogo')
      }
    }
  } catch (err) {
    console.error('Error loading catalog:', err)
    error.value = 'Si è verificato un errore nel caricamento del catalogo'
  } finally {
    loading.value = false
  }
}

function handleFiltersApplied(filteredData: any[]) {
  itemsByGame.value = { 'Risultati Filtro': filteredData }
}

async function clearFilters() {
  // Reset URL parameters
  router.push('/catalogo')
  
  // Reset catalogo data
  itemsByGame.value = {}
  
  // Reload catalog data
  await loadCatalogo()
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
  padding: 2rem;
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

.catalogo-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
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

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--on-surface);
  font-size: 1.2rem;
}

.error {
  text-align: center;
  padding: 2rem;
  color: var(--error);
  font-size: 1.2rem;
}

@media (max-width: 768px) {
  .catalogo-layout {
    flex-direction: column;
  }
  
  .catalogo-main {
    width: 100%;
  }
  
  .catalogo-cards {
    grid-template-columns: 1fr;
  }
}
</style> 