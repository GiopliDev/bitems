<template>
  <div class="item-card" @click="goToDetail">
    <div class="item-img">
      <img 
        :src="item.image ? `/bitems/frontend/UploadedImages/${item.image}` : 'https://via.placeholder.com/440x180?text=Item'" 
        alt="item image" 
      />
    </div>
    <div class="item-body">
      <div class="item-title">{{ item.art_titolo }}</div>
      <div class="item-meta">
        <div class="item-badges">
          <span class="game-badge">{{ item.game_name }}</span>
          <span class="category-badge">{{ item.category_name }}</span>
        </div>
        <span class="item-price">{{ formatPrice(item.art_prezzoUnitario) }} €</span>
      </div>
      <div class="item-tags">
        <span v-for="tag in item.tags" :key="tag" class="item-tag">#{{ tag }}</span>
      </div>
      <!-- item status -->
      <div class="item-status">
        <span style="color: green;" v-if="item.art_qtaDisp > 0" class="item-tag">Available</span>
        <span style="color: red;" v-else class="item-tag">Out of stock</span>
      </div>
      <div class="item-footer">
        <div class="seller-info">
          <span class="seller-name">{{ item.seller_name }}</span>
          <span class="seller-rep" :class="getReputationClass(item.seller_rep)">{{ item.seller_rep }}</span>
        </div>
        <span class="item-qty">Qta: {{ item.art_qtaDisp }}</span>
        <div class="item-actions" @click.stop>
          <button v-if="!isPurchased" @click.stop="purchase" class="buy-btn">Acquista</button>
          <button v-else @click.stop="openChat" class="message-btn">
            <i class="fa-regular fa-message"></i> Invia messaggio rapido
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { defineProps } from 'vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  item: {
    type: Object,
    required: true
  }
})

const router = useRouter()
const isPurchased = ref(false)

async function checkPurchaseStatus() {
  try {
    const response = await axios.get(`http://localhost:80/bitems/frontend/backend/checkPurchase.php?itemId=${props.item.art_id}`)
    if (response.data.success) {
      isPurchased.value = response.data.purchased
    }
  } catch (error) {
    console.error('Error checking purchase status:', error)
  }
}

onMounted(() => {
  checkPurchaseStatus()
})

function goToDetail() {
  router.push({ path: '/itemDetail', query: { id: props.item.art_id } })
}

function formatPrice(price: number): string {
  return price.toFixed(2)
}

function getReputationClass(rep: number): string {
  if (rep >= 100) return 'rep-excellent'
  if (rep >= 50) return 'rep-good'
  if (rep >= 20) return 'rep-average'
  return 'rep-bad'
}

async function purchase(e: Event) {
  e.stopPropagation()
  try {
    const response = await axios.post('http://localhost:80/bitems/frontend/backend/processPayment.php', {
      itemId: props.item.art_id,
      quantity: 1
    })
    
    if (response.data.success) {
      isPurchased.value = true
      props.item.art_qtaDisp--
      await checkPurchaseStatus()
      alert('Acquisto completato con successo!')
    }
  } catch (error: any) {
    if (error.response?.data?.error) {
      alert(error.response.data.error)
    } else {
      alert('Si è verificato un errore durante l\'acquisto')
    }
  }
}

function openChat(e: Event) {
  e.stopPropagation()
  router.push({
    name: 'chats',
    query: { 
      itemId: props.item.art_id,
      sellerId: props.item.art_ute_id,
      itemTitle: props.item.art_titolo
    }
  })
}
</script>

<style scoped>
.item-card {
  background: var(--surface);
  border-radius: 12px;
  box-shadow: 0 2px 12px 0 rgba(60, 30, 90, 0.08);
  width: 260px;
  display: flex;
  flex-direction: column;
  border: 1px solid var(--surface-light);
  transition: box-shadow 0.18s, border-color 0.18s, transform 0.18s;
  position: relative;
  cursor: pointer;
}

.item-card:hover {
  box-shadow: 0 4px 16px 0 rgba(60, 30, 90, 0.13);
  border-color: var(--primary-light);
  transform: translateY(-2px);
}

.item-img {
  background: var(--surface-light);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 140px;
  border-radius: 12px 12px 0 0;
  overflow: hidden;
}

.item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px 12px 0 0;
}

.item-body {
  padding: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.item-title {
  font-weight: 600;
  color: var(--on-surface);
  font-size: 0.95rem;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-meta {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.item-badges {
  display: flex;
  gap: 0.35rem;
  align-items: center;
  flex-wrap: wrap;
}

.game-badge {
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.15rem 0.6rem;
  border-radius: 999px;
  letter-spacing: 0.01em;
  opacity: 0.92;
}

.category-badge {
  background: var(--secondary);
  color: #18181c;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.15rem 0.6rem;
  border-radius: 999px;
  letter-spacing: 0.01em;
  opacity: 0.92;
}

.item-price {
  font-weight: 700;
  color: var(--secondary);
  font-size: 1.1rem;
  margin-top: 0.2rem;
}

.item-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
  margin-bottom: 0.1rem;
}

.item-tag {
  background: var(--surface-light);
  color: var(--primary-light);
  border-radius: 4px;
  padding: 0.1rem 0.5rem;
  font-size: 0.8rem;
  font-weight: 500;
  letter-spacing: 0.01em;
  opacity: 0.85;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--on-surface);
  font-size: 0.85rem;
  margin-top: 0.2rem;
  padding-top: 0.2rem;
  border-top: 1px solid var(--surface-light);
}

.seller-info {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.seller-name {
  color: var(--primary-light);
  font-weight: 500;
}

.seller-rep {
  font-weight: 700;
  padding: 0.1rem 0.35rem;
  border-radius: 4px;
  font-size: 0.8rem;
}

.rep-excellent {
  background: #4CAF50;
  color: white;
}

.rep-good {
  background: #8BC34A;
  color: white;
}

.rep-average {
  background: #FFC107;
  color: #18181c;
}

.rep-bad {
  background: #FF5722;
  color: white;
}

.item-qty {
  color: var(--primary-light);
  font-weight: 600;
}

.item-actions {
  display: flex;
  gap: 0.5rem;
}

.buy-btn {
  background: var(--primary-light);
  color: var(--on-primary);
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.18s;
}

.buy-btn:hover {
  background: var(--secondary);
  color: #18181c;
}

.message-btn {
  background: var(--primary-light);
  color: var(--on-primary);
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.18s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.message-btn:hover {
  background: var(--secondary);
  color: #18181c;
}

.message-btn i {
  font-size: 1rem;
}
</style> 