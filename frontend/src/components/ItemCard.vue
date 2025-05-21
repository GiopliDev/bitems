<template>
  <div class="item-card" @click="goToDetail">
    <div class="item-img">
      <img :src="item.img || 'https://via.placeholder.com/440x180?text=Item'" alt="item image" />
    </div>
    <div class="item-body">
      <div class="item-title">{{ item.itemName }}</div>
      <div class="item-meta">
        <div class="item-badges">
          <span class="game-badge">{{ item.gameName }}</span>
          <span class="category-badge">{{ item.category }}</span>
        </div>
        <span class="item-price">{{ item.price }} €</span>
      </div>
      <div class="item-tags">
        <span v-for="tag in item.tags" :key="tag" class="item-tag">#{{ tag }}</span>
      </div>
      <!-- item status -->
      <div class="item-status">
        <span style="color: green;" v-if="item.status === 'D'" class="item-tag">Available</span> <!-- colore verde acceso -->
        <span style="color: red;" v-if="item.status === 'E'" class="item-tag">Out of stock</span> <!-- colore rosso -->
      </div>
      <div class="item-footer">
        <span class="item-user"><i class="fa-regular fa-user"></i> {{ item.user }}</span>
        <span class="item-qty">Qta: {{ item.qty }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const props = defineProps<{ item: any }>()
const router = useRouter()

function goToDetail() {
  router.push({ path: '/itemDetail', query: { id: props.item.id } })
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
.item-user {
  display: flex;
  align-items: center;
  gap: 0.3rem;
}
.item-qty {
  color: var(--primary-light);
  font-weight: 600;
}
</style> 