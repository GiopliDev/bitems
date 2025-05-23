<template>
  <aside class="filter-bar">
    <h3>Filtra oggetti</h3>
    <div class="filter-group">
      <label for="title">Cerca per titolo:</label>
      <input 
        type="text" 
        id="title" 
        v-model="filters.title" 
        placeholder="Inserisci il titolo..." 
        class="text-input"
      />
    </div>
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
      <div class="tag-input-container">
        <input 
          type="text" 
          id="tags" 
          v-model="tagSearch" 
          placeholder="Inserisci tag (min 3 caratteri)" 
          class="text-input"
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
    <div class="filter-actions">
      <button class="main-btn" @click="apply">Applica e Cerca</button>
      <a href="/catalogo" class="main-btn outline">Cancella filtri</a>
    </div>
  </aside>
</template>

//DA FARE:
//filtri per tag
//checkbo per solo quelli Available

<script setup lang="ts">
import { reactive, ref, watch, onMounted } from 'vue'
import axios from '@/config/axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const emit = defineEmits<{
  (e: 'filters-applied', data: any[]): void
}>()

const filters = reactive<{
  title: string;
  game: string;
  category: string;
  minPrice: number;
  maxPrice: number;
  onlyAvailable: boolean;
  tags: string[];
}>({
  title: '',
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

watch(tagSearch, async (newValue) => {
  if (newValue.length >= 3) {
    const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
      params: { 
        action: 'searchTags',
        query: newValue
      }
    })
    
    if (response.data.code === 0) {
      showTagSuggestions.value = false
    } else {
      tagSuggestions.value = response.data
      showTagSuggestions.value = true
    }
  } else {
    showTagSuggestions.value = false
    tagSuggestions.value = []
  }
})
 
function addTag(tag: string) {
  if (!filters.tags.includes(tag)) {
    filters.tags.push(tag)
  }
  tagSearch.value = ''
  showTagSuggestions.value = false
}

function removeTag(tag: string) {
  filters.tags = filters.tags.filter(t => t !== tag)
}

async function apply() {
  try {
    const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
      params: {
        action: 'getCatalogoFiltrato',
        title: filters.title,
        gameName: filters.game,
        category: filters.category,
        minPrice: filters.minPrice,
        maxPrice: filters.maxPrice,
        onlyAvailable: filters.onlyAvailable,
        tags: filters.tags.join(',')
      }
    })
    
    emit('filters-applied', response.data.data)
  } catch (error) {
    console.error('Error applying filters:', error)
  }
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
  gap: 1.5rem;
  font-size: 1.1rem;
  border-left: 4px solid var(--primary-light);
  z-index: 10;
  overflow-y: auto;
}

.filter-bar h3 {
  color: var(--primary-light);
  margin-bottom: 0.5rem;
  font-size: 1.3rem;
  font-weight: 700;
  letter-spacing: 0.01em;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  color: var(--primary-light);
  font-size: 1rem;
  font-weight: 600;
}

.text-input, select, input[type="number"] {
  background: var(--surface-light);
  border: 1.5px solid var(--primary-light);
  border-radius: 8px;
  padding: 0.6rem 0.8rem;
  color: var(--on-surface);
  font-size: 1rem;
  outline: none;
  transition: all 0.2s;
  width: 100%;
}

.text-input:focus, select:focus, input[type="number"]:focus {
  border-color: var(--secondary);
  box-shadow: 0 0 0 2px rgba(var(--secondary-rgb), 0.2);
}

input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: var(--primary-light);
  cursor: pointer;
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

.filter-actions {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  margin-top: 0.5rem;
}

.main-btn {
  padding: 0.8rem 1.5rem;
  border: 2px solid var(--primary-light);
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
  text-decoration: none;
}

.main-btn:hover {
  background: var(--secondary);
  color: #18181c;
  border-color: var(--secondary);
  transform: translateY(-1px);
}

.main-btn.outline {
  background: transparent;
  color: var(--primary-light);
}

.main-btn.outline:hover {
  background: var(--surface-light);
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
  margin-top: 0.3rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.tag-suggestion {
  padding: 0.6rem 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.tag-suggestion:hover {
  background: var(--surface-light);
  color: var(--secondary);
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
  font-size: 0.9rem;
}

.remove-tag {
  background: none;
  border: none;
  color: var(--on-primary);
  cursor: pointer;
  padding: 0;
  font-size: 1.2rem;
  line-height: 1;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.remove-tag:hover {
  opacity: 1;
  color: var(--secondary);
}
</style> 