<template>
  <div class="history-view">
    <h1>I miei acquisti</h1>
    <div class="history-list">
      <div v-for="item in historyItems" :key="item.id" 
           class="history-item"
           @click="goToItem(item.art_id)"
           role="button"
           tabindex="0"
           @keyup.enter="goToItem(item.art_id)">
        <div class="item-image">
          <img 
            :src="`/bitems/frontend/UploadedImages/${item.imageId}`" 
            :alt="item.title"
            class="item-img"
          />
        </div>
        <div class="item-details">
          <h3>{{ item.title }}</h3>
          <div class="item-meta">
            <span class="item-date">
              <i class="far fa-calendar"></i>
              {{ item.date }}
            </span>
            <span class="item-seller">
              <i class="far fa-user"></i>
              Venduto da {{ item.seller }}
            </span>
            <span class="item-quantity">
              <i class="fas fa-shopping-cart"></i>
              Quantità: {{ item.quantity }}
            </span>
          </div>
          <div class="item-review" v-if="item.review">
            <div class="review-rating">
              <i :class="['fas', item.review.rating ? 'fa-thumbs-up' : 'fa-thumbs-down']"></i>
              {{ item.review.rating ? 'Positiva' : 'Negativa' }}
            </div>
            <p class="review-text">{{ item.review.description }}</p>
          </div>
          <div v-else class="item-status" :class="item.status">
            {{ item.status === 'pending_review' ? 'In attesa di recensione' : 'Completato' }}
          </div>
        </div>
        <div class="item-price">
          <div class="price-details">
            <span class="unit-price">{{ item.unitPrice }}€ x {{ item.quantity }}</span>
            <span class="total-price">{{ item.totalPrice }}€</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/config/axios'

interface Review {
  rating: boolean
  description: string
}

interface HistoryItem {
  id: number
  title: string
  date: string
  quantity: number
  unitPrice: number
  totalPrice: number
  seller: string
  status: string
  review: Review | null
  imageId: number
  art_id: number
}

const historyItems = ref<HistoryItem[]>([])
const router = useRouter()

async function fetchHistory() {
  try {
    const response = await axios.get('/bitems/frontend/backend/getHistory.php', {
      withCredentials: true
    })
    if (response.data.success) {
      historyItems.value = response.data.history
    } else {
      console.error('Error fetching history:', response.data.message)
    }
  } catch (error) {
    console.error('Error fetching history:', error)
  }
}

function goToItem(itemId: number) {
  router.push({
    path: '/itemDetail',
    query: { id: itemId }
  })
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
  gap: 1.5rem;
}

.history-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 1.5rem;
  display: grid;
  grid-template-columns: 120px 1fr auto;
  gap: 1.5rem;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.history-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.history-item:focus {
  outline: 2px solid var(--primary-light);
  outline-offset: 2px;
}

.item-image {
  width: 120px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
  background: var(--surface-light);
}

.item-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.item-details h3 {
  color: var(--on-surface);
  margin: 0;
  font-size: 1.2rem;
}

.item-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  font-size: 0.9rem;
  color: var(--on-surface);
  opacity: 0.8;
}

.item-meta span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-review {
  background: var(--surface-light);
  padding: 0.8rem;
  border-radius: 8px;
  margin-top: 0.5rem;
}

.review-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--secondary);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.review-text {
  color: var(--on-surface);
  margin: 0;
  font-size: 0.9rem;
  line-height: 1.4;
}

.item-status {
  display: inline-block;
  padding: 0.4rem 1rem;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 500;
}

.item-status.completed {
  background: var(--secondary);
  color: #18181c;
}

.item-status.pending_review {
  background: var(--primary-light);
  color: var(--on-primary);
}

.item-price {
  text-align: right;
  min-width: 120px;
}

.price-details {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.unit-price {
  color: var(--on-surface);
  opacity: 0.8;
  font-size: 0.9rem;
}

.total-price {
  color: var(--secondary);
  font-size: 1.3rem;
  font-weight: 700;
}
</style> 