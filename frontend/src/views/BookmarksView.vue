<template>
  <div class="bookmarks-view">
    <h1>Segnalibri</h1>
    <div v-if="loading" class="loading">
      Caricamento segnalibri...
    </div>
    <div v-else-if="error" class="error">
      {{ error }}
    </div>
    <div v-else-if="bookmarkedItems.length === 0" class="no-bookmarks">
      <p>Non hai ancora salvato nessun articolo nei segnalibri</p>
    </div>
    <div v-else class="bookmarks-grid">
      <div v-for="item in bookmarkedItems" :key="item.art_id" class="bookmark-item">
        <button class="remove-bookmark" @click.stop="removeBookmark(item.art_id)">
          <i class="fas fa-trash"></i>
        </button>
        <ItemCard :item="item" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/config/axios'
import ItemCard from '@/components/ItemCard.vue'

const bookmarkedItems = ref([])
const loading = ref(true)
const error = ref('')

async function fetchBookmarks() {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: 'getBookmarks'
    })
    
    if (response.data.success) {
      bookmarkedItems.value = response.data.data
    } else {
      error.value = 'Errore nel caricamento dei segnalibri'
    }
  } catch (err) {
    console.error('Error fetching bookmarks:', err)
    error.value = 'Si è verificato un errore nel caricamento dei segnalibri'
  } finally {
    loading.value = false
  }
}

async function removeBookmark(articleId) {
  try {
    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: 'removeBookmark',
      id_articolo: articleId
    })
    
    if (response.data.success) {
      // Rimuovi l'articolo dalla lista locale
      bookmarkedItems.value = bookmarkedItems.value.filter(item => item.art_id !== articleId)
    } else {
      console.error('Error removing bookmark:', response.data.error)
    }
  } catch (error) {
    console.error('Error removing bookmark:', error)
  }
}

onMounted(() => {
  fetchBookmarks()
})
</script>

<style scoped>
.bookmarks-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  color: var(--primary-light);
  font-size: 2rem;
  margin-bottom: 2rem;
  font-weight: 700;
}

.bookmarks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  padding: 1rem;
}

.bookmark-item {
  position: relative;
}

.remove-bookmark {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  color: var(--error);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1;
  transition: all 0.2s;
  font-size: 1.1rem;
  padding: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.remove-bookmark:hover {
  transform: scale(1.1);
  background-color: var(--error);
  color: white;
}

.loading, .error, .no-bookmarks {
  text-align: center;
  padding: 3rem;
  color: var(--on-surface);
  font-size: 1.2rem;
}

.error {
  color: var(--error);
}

.no-bookmarks {
  color: var(--on-surface);
  opacity: 0.7;
  font-style: italic;
}

@media (max-width: 768px) {
  .bookmarks-view {
    padding: 1rem;
  }
  
  .bookmarks-grid {
    grid-template-columns: 1fr;
  }
}
</style> 