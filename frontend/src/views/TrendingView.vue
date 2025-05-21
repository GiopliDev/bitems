<template>
  <div class="trending-view">
    <h1>Trending Items</h1>
    <div class="trending-grid">
      <ItemCard v-for="item in trendingItems" :key="item.id" :item="item" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/config/axios'
import ItemCard from '@/components/ItemCard.vue'

const trendingItems = ref([])

async function fetchTrendingItems() {
  try {
    const response = await axios.get('/bitems/frontend/backend/getTrendingItems.php')
    if (response.data.success) {
      trendingItems.value = response.data.items
    } else {
      console.error('Error fetching trending items:', response.data.message)
    }
  } catch (error) {
    console.error('Error fetching trending items:', error)
  }
}

onMounted(() => {
  fetchTrendingItems()
})
</script>

<style scoped>
.trending-view {
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

.trending-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  padding: 1rem;
}

@media (max-width: 768px) {
  .trending-view {
    padding: 1rem;
  }
  
  .trending-grid {
    grid-template-columns: 1fr;
  }
}
</style> 