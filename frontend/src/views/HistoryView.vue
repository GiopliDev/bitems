<template>
  <div class="history-view">
    <h1>Cronologia</h1>
    <div class="history-list">
      <div v-for="item in historyItems" :key="item.id" class="history-item">
        <div class="item-info">
          <h3>{{ item.title }}</h3>
          <p>{{ item.date }}</p>
          <p class="item-status" :class="item.status.toLowerCase()">{{ item.status }}</p>
        </div>
        <div class="item-price">{{ item.price }}€</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/config/axios'

const historyItems = ref([])

async function fetchHistory() {
  try {
    const response = await axios.get('http://localhost:5173/bitems/frontend/backend/getHistory.php')
    if (response.data.success) {
      historyItems.value = response.data.items
    } else {
      console.error('Error fetching history:', response.data.message)
    }
  } catch (error) {
    console.error('Error fetching history:', error)
  }
}

onMounted(() => {
  fetchHistory()
})
</script>

<style scoped>
.history-view {
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

.history-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.history-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.item-info h3 {
  color: var(--on-surface);
  margin: 0 0 0.5rem 0;
  font-size: 1.2rem;
}

.item-info p {
  color: var(--on-surface);
  margin: 0;
  opacity: 0.8;
}

.item-status {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  border-radius: 999px;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.item-status.completed {
  background: var(--secondary);
  color: #18181c;
}

.item-status.pending {
  background: var(--primary-light);
  color: var(--on-primary);
}

.item-status.cancelled {
  background: var(--error);
  color: var(--on-error);
}

.item-price {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--secondary);
}
</style> 