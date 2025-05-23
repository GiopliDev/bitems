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
      <label for="availabilityCheckBox">Solo Disponibili:</label>
      <input type="checkbox" id="availabilityCheckBox" v-model="filters.onlyAvailable" />
    </div>
    <div class="filter-group">
      <label for="tags">Tag:</label>
      <div v-if="createTagButton" class="create-tag-button">
        <button @click="createTag">Crea Tag</button>
      </div>
      <div class="tag-input-container">
        <input 
          type="text" 
          id="tags" 
          v-model="tagSearch" 
          placeholder="Inserisci tag (min 3 caratteri)" 
        />
        <div v-if="showTagSuggestions" class="tag-suggestions">
          <div 
            v-for="tag in tagSuggestions" 
            :key="tag"
            class="tag-suggestion"
            @click="addTag(tag)"
          >
            {{ tag }}
          </div>
        </div>
      </div>
      <div class="selected-tags">
        <span 
          v-for="tag in filters.tags" 
          :key="tag" 
          class="selected-tag"
        >
          {{ tag }}
          <button @click="removeTag(tag)" class="remove-tag">&times;</button>
        </span>
      </div>
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

//DA FARE:
//filtri per tag
//checkbo per solo quelli Available

<script setup lang="ts">
import { reactive, ref, watch, onMounted } from 'vue'
import axios from '@/config/axios'

const emit = defineEmits<{
  (e: 'filters-applied', data: any[]): void
}>()

const filters = reactive<{
  game: string;
  category: string;
  minPrice: number;
  maxPrice: number;
  onlyAvailable: boolean;
  tags: string[];
}>({
  game: '',
  category: '',
  minPrice: 0,
  maxPrice: 2000,
  onlyAvailable: false,
  tags: []
})

const games = ref<string[]>([])
const categories = ref<string[]>([])
const tagSearch = ref('')
const tagSuggestions = ref<string[]>([])
const showTagSuggestions = ref(false)
const createTagButton = ref(false)
// Load games and categories on mount
onMounted(async () => {
  try {
    const gamesRes = await axios.get('/bitems/frontend/backend/getCatalogo.php', { params: { action: 'getGames' } })
    const categoriesRes = await axios.get('/bitems/frontend/backend/getCatalogo.php', { params: { action: 'getCategories' } })
    games.value = gamesRes.data
    categories.value = categoriesRes.data
  } catch (error) {
    console.error('Error loading filter data:', error)
  }
})

// watch ha la stessa funzione di observable su angular 
watch(tagSearch, async (newValue) => {
  if (newValue.length >= 3) {
    const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
      params: { 
        action: 'searchTags',
        query: newValue
      }
    })
    
    if (response.data.code === 0) {
      createTagButton.value = true
      showTagSuggestions.value = false
    } else {
      tagSuggestions.value = response.data
      showTagSuggestions.value = true
      createTagButton.value = false
    }
  } else {
    showTagSuggestions.value = false
    tagSuggestions.value = []
    createTagButton.value = false
  }
})
 
function addTag(tag: string) {
  if (!filters.tags.includes(tag)) {
    filters.tags.push(tag)
  }
  tagSearch.value = ''
  showTagSuggestions.value = false
}

async function createTag() {
  const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
    params: {
      action: 'createTag',
      tag: tagSearch.value
    }
  })
}
function removeTag(tag: string) {
  filters.tags = filters.tags.filter(t => t !== tag)
}


//NOTA BENE: se viene composto l url con i parametri e poi viene aggiornata la pagina non funziona,va fatta una richiesta con axios e poi popolare con le card
async function apply() {
  try {
    const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
      params: {
        action: 'getCatalogoFiltrato',
        gameName: filters.game,
        category: filters.category,
        minPrice: filters.minPrice,
        maxPrice: filters.maxPrice,
        onlyAvailable: filters.onlyAvailable,
        tags: filters.tags.join(',')
      }
    })
    
    // Emetti l'evento con i dati filtrati
    emit('filters-applied', response.data.data)
  } catch (error) {
    console.error('Error applying filters:', error)
  }
}

function reset() {
  filters.game = ''
  filters.category = ''
  filters.minPrice = 0
  filters.maxPrice = 2000
  filters.onlyAvailable = false
  filters.tags = []
  tagSearch.value = ''
  showTagSuggestions.value = false
}
</script>

<style scoped>
.filter-bar {
  position: sticky;
  top: 0;
  right: 0;
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

.tag-input-container {
  position: relative;
}

.tag-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--surface);
  border: 1px solid var(--primary-light);
  border-radius: 8px;
  max-height: 200px;
  overflow-y: auto;
  z-index: 100;
}

.tag-suggestion {
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.tag-suggestion:hover {
  background: var(--surface-light);
}

.selected-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.selected-tag {
  background: var(--primary-light);
  color: var(--on-primary);
  padding: 0.3rem 0.6rem;
  border-radius: 4px;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.remove-tag {
  background: none;
  border: none;
  color: var(--on-primary);
  cursor: pointer;
  padding: 0;
  font-size: 1.2rem;
  line-height: 1;
}

.remove-tag:hover {
  color: var(--secondary);
}
</style> 