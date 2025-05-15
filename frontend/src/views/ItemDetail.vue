<template>
  <div class="item-detail-layout" v-if="item">
    <div class="item-main">
      <h2 class="item-title">{{ item.itemName }}</h2>
      <div class="item-slider">
        <button class="slider-btn" @click="prevImage">&#8592;</button>
        <img :src="currentImage" alt="item image" class="slider-img" />
        <button class="slider-btn" @click="nextImage">&#8594;</button>
      </div>
      <div class="slider-dots">
        <span v-for="(img, idx) in item.images" :key="idx" :class="['dot', {active: idx === imageIndex}]" @click="goToImage(idx)"></span>
      </div>
      <div class="item-info-row">
        <span class="item-qty">Remaining: {{ item.qty }}</span>
        <span class="item-price">Price: {{ item.price }}$/piece</span>
      </div>
      <div class="item-tags">
        <span v-for="tag in item.tags" :key="tag" class="item-tag">#{{ tag }}</span>
      </div>
      <div class="item-desc-box">
        <p class="item-desc">" {{ item.description }} "</p>
      </div>
      <div class="item-reviews">
        <h3>Recensioni</h3>
        <div v-for="review in reviews" :key="review.id" class="review">
          <div class="review-header">
            <span class="review-user">{{ review.user }}</span>
            <span class="review-date">{{ review.date }}</span>
          </div>
          <div class="review-body">{{ review.text }}</div>
          <div class="review-actions">
            <span class="like"><i class="fa-regular fa-thumbs-up"></i> {{ review.likes }}</span>
            <span class="dislike"><i class="fa-regular fa-thumbs-down"></i> {{ review.dislikes }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="item-side">
      <div class="user-box">
        <div class="user-avatar"><i class="fa-regular fa-user"></i></div>
        <div class="user-info">
          <div class="user-name">{{ item.user }}</div>
          <div class="user-rep">Reputazione: {{ user.rep }}</div>
        </div>
      </div>
      <div class="order-summary">
        <h4>Riepilogo ordine</h4>
        <div class="order-row">
          <span>{{ item.itemName }} x{{ orderQty }}</span>
          <span>{{ (item.price * orderQty).toFixed(2) }}$</span>
        </div>
        <div class="order-row">
          <span>Tasse</span>
          <span>1.00$</span>
        </div>
        <div class="order-total">
          <span>Totale</span>
          <span>{{ (item.price * orderQty + 1).toFixed(2) }}$</span>
        </div>
        <div class="order-qty-select">
          <button @click="orderQty > 1 && orderQty--">-</button>
          <span>{{ orderQty }}</span>
          <button @click="orderQty < item.qty && orderQty++">+</button>
        </div>
        <button class="main-btn">Procedi con l'ordine</button>
      </div>
    </div>
  </div>
  <div v-else style="padding:2rem; color:var(--error,red); font-size:1.2rem;">Oggetto non trovato.</div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import itemsData from '../jsons/items.json'

interface Item {
  id: number
  itemName: string
  price: number
  qty: number
  tags: string[]
  user: string
  description: string
  images: string[]
  gameName: string
  isHot: boolean
  createdAt: string
}

const route = useRoute()
const item = ref<Item | null>(null)
const imageIndex = ref(0)
const orderQty = ref(1)
const currentImage = ref('')

const user = { rep: 12 }
const reviews = [
  { id: 1, user: 'UserA', date: '2024-06-01', text: 'Ottimo venditore!', likes: 5, dislikes: 0 },
  { id: 2, user: 'UserB', date: '2024-06-02', text: 'Tutto perfetto, consigliato.', likes: 3, dislikes: 0 },
  { id: 3, user: 'UserC', date: '2024-06-03', text: 'Spedizione veloce.', likes: 2, dislikes: 1 }
]

onMounted(() => {
  const id = Number(route.query.id)
  const found = (itemsData as Item[]).find(i => i.id === id)
  item.value = found || null
  if (item.value && item.value.images.length > 0) {
    currentImage.value = item.value.images[0]
  }
})

function prevImage() {
  if (!item.value) return
  imageIndex.value = (imageIndex.value - 1 + item.value.images.length) % item.value.images.length
  currentImage.value = item.value.images[imageIndex.value]
}
function nextImage() {
  if (!item.value) return
  imageIndex.value = (imageIndex.value + 1) % item.value.images.length
  currentImage.value = item.value.images[imageIndex.value]
}
function goToImage(idx: number) {
  if (!item.value) return
  imageIndex.value = idx
  currentImage.value = item.value.images[imageIndex.value]
}
</script>

<style scoped>
.item-detail-layout {
  display: flex;
  gap: 2rem;
  padding: 2rem 1rem;
  align-items: flex-start;
}
.item-main {
  flex: 2;
  background: var(--surface);
  border-radius: 18px;
  padding: 2rem 2.5rem;
  box-shadow: 0 2px 16px #0002;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.item-title {
  color: var(--primary-light);
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1.2rem;
}
.item-slider {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-bottom: 0.7rem;
}
.slider-img {
  width: 320px;
  height: 180px;
  object-fit: cover;
  border-radius: 12px;
  background: var(--surface-light);
}
.slider-btn {
  background: none;
  border: 2px solid var(--secondary);
  color: var(--secondary);
  border-radius: 50%;
  width: 38px;
  height: 38px;
  font-size: 1.3rem;
  cursor: pointer;
  transition: border-color 0.2s, color 0.2s;
}
.slider-btn:hover {
  border-color: var(--primary-light);
  color: var(--primary-light);
}
.slider-dots {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1.2rem;
}
.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--surface-light);
  opacity: 0.5;
  cursor: pointer;
  transition: background 0.2s, opacity 0.2s;
}
.dot.active {
  background: var(--secondary);
  opacity: 1;
}
.item-info-row {
  display: flex;
  gap: 2.5rem;
  margin-bottom: 0.7rem;
  color: var(--primary-light);
  font-size: 1.08rem;
  font-weight: 500;
}
.item-tags {
  margin-bottom: 0.7rem;
}
.item-tag {
  background: #ffd600;
  color: #222;
  border-radius: 8px;
  padding: 0.13rem 0.7rem;
  font-size: 0.98rem;
  margin-right: 0.3rem;
  font-weight: 600;
  letter-spacing: 0.01em;
}
.item-desc-box {
  background: #4446;
  border-radius: 10px;
  padding: 1rem 1.2rem;
  margin-bottom: 1.2rem;
  color: var(--on-surface);
  font-size: 1.05rem;
}
.item-desc {
  margin: 0;
}
.item-reviews {
  margin-top: 2.5rem;
}
.item-reviews h3 {
  color: var(--primary-light);
  margin-bottom: 1rem;
}
.review {
  background: var(--surface-light);
  border-radius: 8px;
  padding: 0.8rem 1rem;
  margin-bottom: 1rem;
}
.review-header {
  display: flex;
  justify-content: space-between;
  font-size: 0.98rem;
  color: var(--primary-light);
  margin-bottom: 0.3rem;
}
.review-body {
  color: var(--on-surface);
  margin-bottom: 0.4rem;
}
.review-actions {
  display: flex;
  gap: 1.2rem;
  color: var(--secondary);
  font-size: 1.1rem;
  align-items: center;
}
.like, .dislike {
  display: flex;
  align-items: center;
  gap: 0.3rem;
}
.item-side {
  flex: 1;
  background: var(--surface);
  border-radius: 18px;
  padding: 2rem 1.5rem;
  box-shadow: 0 2px 16px #0002;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
  min-width: 270px;
  max-width: 350px;
}
.user-box {
  display: flex;
  align-items: center;
  gap: 1.1rem;
  margin-bottom: 1.5rem;
}
.user-avatar {
  width: 48px;
  height: 48px;
  background: var(--surface-light);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: var(--primary-light);
}
.user-info {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.user-name {
  font-weight: 700;
  color: var(--primary-light);
  font-size: 1.1rem;
}
.user-rep {
  color: var(--secondary);
  font-size: 1rem;
}
.order-summary {
  background: var(--surface-light);
  border-radius: 12px;
  padding: 1.2rem 1.2rem 1.5rem 1.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
.order-summary h4 {
  color: var(--primary-light);
  margin-bottom: 0.7rem;
  font-size: 1.1rem;
}
.order-row {
  display: flex;
  justify-content: space-between;
  color: var(--on-surface);
  font-size: 1.05rem;
}
.order-total {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  color: var(--secondary);
  font-size: 1.13rem;
  margin-top: 0.5rem;
  border-top: 1.5px solid #4446;
  padding-top: 0.5rem;
}
.order-qty-select {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin: 1.2rem 0 0.7rem 0;
  justify-content: center;
}
.order-qty-select button {
  background: none;
  border: 2px solid var(--secondary);
  color: var(--secondary);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  font-size: 1.2rem;
  cursor: pointer;
  transition: border-color 0.2s, color 0.2s;
}
.order-qty-select button:hover {
  border-color: var(--primary-light);
  color: var(--primary-light);
}
.main-btn {
  width: 100%;
  margin-top: 0.7rem;
  padding: 0.8rem 0;
  border: 2px solid var(--primary-light);
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.main-btn:hover {
  background: var(--secondary);
  color: #18181c;
}
</style> 