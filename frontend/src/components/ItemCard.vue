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
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { defineProps } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  }
})

const router = useRouter()

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
</script>

<style scoped>
.item-card {
  background: var(--surface);
  border-radius: 16px;
  box-shadow: 0 2px 12px 0 rgba(60, 30, 90, 0.08);
  min-width: 380px;
  max-width: 440px;
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  border: 1.5px solid var(--surface-light);
  transition: box-shadow 0.18s, border-color 0.18s, transform 0.18s;
  position: relative;
  cursor: pointer;
}

.item-card:hover {
  box-shadow: 0 6px 32px 0 rgba(60, 30, 90, 0.13);
  border-color: var(--primary-light);
  transform: translateY(-3px) scale(1.012);
}

.item-img {
  background: var(--surface-light);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 180px;
  border-radius: 16px 16px 0 0;
  overflow: hidden;
}

.item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px 16px 0 0;
}

.item-body {
  padding: 1.2rem 1.5rem 1.2rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}

.item-title {
  font-weight: 700;
  color: var(--on-surface);
  font-size: 1.18rem;
  margin-bottom: 0.1rem;
  letter-spacing: 0.01em;
}

.item-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.2rem;
}

.item-badges {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.game-badge {
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 600;
  font-size: 0.98rem;
  padding: 0.18rem 0.8rem;
  border-radius: 999px;
  letter-spacing: 0.01em;
  opacity: 0.92;
}

.category-badge {
  background: var(--secondary);
  color: #18181c;
  font-weight: 600;
  font-size: 0.98rem;
  padding: 0.18rem 0.8rem;
  border-radius: 999px;
  letter-spacing: 0.01em;
  opacity: 0.92;
}

.item-price {
  font-weight: 700;
  color: var(--secondary);
  font-size: 1.08rem;
}

.item-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.3rem;
  margin-bottom: 0.1rem;
}

.item-tag {
  background: var(--surface-light);
  color: var(--primary-light);
  border-radius: 6px;
  padding: 0.08rem 0.6rem;
  font-size: 0.93rem;
  font-weight: 500;
  letter-spacing: 0.01em;
  opacity: 0.85;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--on-surface);
  font-size: 0.98rem;
  margin-top: 0.2rem;
  opacity: 0.85;
}

.seller-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.seller-name {
  color: var(--primary-light);
  font-weight: 500;
}

.seller-rep {
  font-weight: 700;
  padding: 0.1rem 0.4rem;
  border-radius: 4px;
  font-size: 0.9rem;
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
</style> 