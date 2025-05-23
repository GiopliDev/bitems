<template>
  <div class="bookmarks-view">
    <h1>Segnalibri</h1>
    <div class="bookmarks-grid">
      <ItemCard v-for="item in bookmarkedItems" :key="item.id" :item="item" />
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
    const response = await axios.get('http://localhost:5173/bitems/frontend/backend/getBookmarks.php')
    if (response.data.success) {
      bookmarkedItems.value = response.data.items
    } else {
      console.error('Error fetching bookmarks:', response.data.message)
    }
  } catch (error) {
    console.error('Error fetching bookmarks:', error)
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

@media (max-width: 768px) {
  .bookmarks-view {
    padding: 1rem;
  }
  
  .bookmarks-grid {
    grid-template-columns: 1fr;
  }
}
</style> 