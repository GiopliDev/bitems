<template>
  <div class="bookmarks-view">
    <h1>Segnalibri</h1>
    <div class="bookmarks-grid">
      <div v-for="item in bookmarkedItems" :key="item.art_id" class="bookmark-item">
        <button class="remove-bookmark" @click="removeBookmark(item.art_id)">
          ❌
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

async function fetchBookmarks() {
  try {
    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: 'getBookmarks'
    }, {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    })
    
    if (response.data.success) {
      bookmarkedItems.value = response.data.bookmarks
    } else {
      console.error('Error fetching bookmarks:', response.data.error)
    }
  } catch (error) {
    console.error('Error fetching bookmarks:', error)
  }
}

async function removeBookmark(articleId) {
  try {
    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: 'removeBookmark',
      id_articolo: articleId
    }, {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
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
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: transparent;
  border: none;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1;
  transition: transform 0.2s;
  font-size: 1.2rem;
  padding: 0;
}

.remove-bookmark:hover {
  transform: scale(1.1);
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