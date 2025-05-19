<template>
  <div class="item-detail-layout" v-if="item">
    <div class="item-main">
      <h2 class="item-title">{{ item.art_titolo }}</h2>
      <div class="item-slider">
        <button class="slider-btn" @click="prevImage">&#8592;</button>
        <img :src="currentImage" alt="item image" class="slider-img" />
        <button class="slider-btn" @click="nextImage">&#8594;</button>
      </div>
      <div class="slider-dots">
        <span v-for="(img, idx) in item.images" :key="idx" :class="['dot', {active: idx === imageIndex}]" @click="goToImage(idx)"></span>
      </div>
      <div class="item-info-row">
        <span class="item-qty">Disponibili: {{ item.art_qtaDisp }}</span>
        <span class="item-price">Prezzo: {{ item.art_prezzoUnitario }}€</span>
      </div>
      <div class="item-tags">
        <span v-for="tag in item.tags" :key="tag" class="item-tag">#{{ tag }}</span>
      </div>
      <div class="item-desc-box">
        <p class="item-desc">{{ item.art_descrizione }}</p>
      </div>
      <div class="item-general-actions">
        <span class="like">
          <i class="fa-regular fa-thumbs-up"></i> {{ generalLikes }}
        </span>
        <span class="dislike">
          <i class="fa-regular fa-thumbs-down"></i> {{ generalDislikes }}
        </span>
        <span class="bookmark">
          <img src="../assets/icons/mark.png" alt="bookmark" style="width: 30px; height: 30px;"/>
          Bookmark
        </span>
      </div>
      <div class="item-reviews">
        <h3>Recensioni</h3>
        <div v-for="review in item.recensioni" :key="review.rec_id" class="review">
          <div class="review-header">
            <span class="review-user">{{ review.ute_username }}</span>
            <span class="review-rating">
              <i v-for="n in 5" :key="n" 
                 :class="['fa-star', n <= review.rec_voto ? 'fa-solid' : 'fa-regular']"></i>
            </span>
          </div>
          <div class="review-body">{{ review.rec_dex }}</div>
        </div>
      </div>
    </div>
    <div class="item-side">
      <div class="user-box">
        <div class="user-avatar"><i class="fa-regular fa-user"></i></div>
        <div class="user-info">
          <div class="user-name">{{ item.ute_username }}</div>
          <div class="user-rep">Reputazione: {{ item.ute_rep }}</div>
        </div>
      </div>
      <div class="order-summary">
        <h4>Riepilogo ordine</h4>
        <div class="order-row">
          <span>{{ item.art_titolo }} x{{ orderQty }}</span>
          <span>{{ (item.art_prezzoUnitario * orderQty).toFixed(2) }}€</span>
        </div>
        <div class="order-row">
          <span>Tasse</span>
          <span>1.00€</span>
        </div>
        <div class="order-total">
          <span>Totale</span>
          <span>{{ (item.art_prezzoUnitario * orderQty + 1).toFixed(2) }}€</span>
        </div>
        <div class="order-qty-select">
          <button @click="orderQty > 1 && orderQty--">-</button>
          <span>{{ orderQty }}</span>
          <button @click="orderQty < item.art_qtaDisp && orderQty++">+</button>
        </div>
        <button class="main-btn" @click="showPaymentPopup = true">Procedi con l'ordine</button>
      </div>
    </div>
  </div>
  <PaymentPopup v-if="showPaymentPopup" @close="showPaymentPopup = false" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/config/axios'
import PaymentPopup from '../components/PaymentPopup.vue'

interface Review {
  rec_id: number
  rec_art_id: number
  rec_ute_id: number
  rec_voto: string
  rec_dex: string
  ute_username: string
}

interface Item {
  art_id: number
  art_titolo: string
  art_qtaDisp: number
  art_prezzoUnitario: number
  art_descrizione: string
  art_timestamp: string
  art_status: string
  ute_id: number
  ute_username: string
  ute_rep: number
  recensioni: Review[]
  tags?: string[]
  images?: string[]
}

const route = useRoute()
const item = ref<Item | null>(null)
const imageIndex = ref(0)
const orderQty = ref(1)
const currentImage = ref('')
const showPaymentPopup = ref(false)
const generalLikes = ref(10)
const generalDislikes = ref(2)

onMounted(async () => {
  try {
    const response = await axios.get(`/frontend/backend/getItem.php?id=${route.query.id}`)
    item.value = response.data
    if (item.value?.images?.length) {
      currentImage.value = item.value.images[0]
    }
  } catch (error) {
    console.error('Errore nel recupero dei dati:', error)
  }
})

function prevImage() {
  if (!item.value?.images?.length) return
  imageIndex.value = (imageIndex.value - 1 + item.value.images.length) % item.value.images.length
  currentImage.value = item.value.images[imageIndex.value]
}

function nextImage() {
  if (!item.value?.images?.length) return
  imageIndex.value = (imageIndex.value + 1) % item.value.images.length
  currentImage.value = item.value.images[imageIndex.value]
}

function goToImage(idx: number) {
  if (!item.value?.images?.length) return
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
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  box-sizing: border-box;
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
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--surface-light);
  cursor: pointer;
  transition: background-color 0.2s;
}

.dot.active {
  background: var(--primary-light);
}

.item-info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.item-qty {
  color: var(--primary-light);
  font-weight: 600;
}

.item-price {
  color: var(--secondary);
  font-weight: 700;
}

.item-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.item-tag {
  background: var(--surface-light);
  color: var(--secondary);
  padding: 0.3rem 0.8rem;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 500;
}

.item-desc-box {
  background: var(--surface-light);
  padding: 1.5rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
}

.item-desc {
  color: var(--on-surface);
  line-height: 1.6;
  font-size: 1.1rem;
}

.item-general-actions {
  display: flex;
  gap: 1.5rem;
  align-items: center;
  margin-bottom: 2rem;
}

.like, .dislike, .bookmark {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  cursor: pointer;
  color: var(--on-surface);
  font-size: 1.1rem;
}

.item-reviews {
  margin-top: 2rem;
}

.item-reviews h3 {
  color: var(--primary-light);
  font-size: 1.3rem;
  margin-bottom: 1rem;
}

.review {
  background: var(--surface-light);
  padding: 1.2rem;
  border-radius: 12px;
  margin-bottom: 1rem;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.8rem;
}

.review-user {
  font-weight: 600;
  color: var(--primary-light);
}

.review-rating {
  color: var(--secondary);
}

.review-body {
  color: var(--on-surface);
  line-height: 1.5;
}

.item-side {
  flex: 1;
  min-width: 300px;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.user-box {
  background: var(--surface);
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 16px #0002;
}

.user-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: var(--surface-light);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  color: var(--primary-light);
}

.user-info {
  flex: 1;
}

.user-name {
  font-weight: 600;
  color: var(--primary-light);
  margin-bottom: 0.3rem;
}

.user-rep {
  color: var(--secondary);
  font-size: 0.9rem;
}

.order-summary {
  background: var(--surface);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 2px 16px #0002;
}

.order-summary h4 {
  color: var(--primary-light);
  font-size: 1.2rem;
  margin-bottom: 1.2rem;
}

.order-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.8rem;
  color: var(--on-surface);
}

.order-total {
  display: flex;
  justify-content: space-between;
  margin: 1rem 0;
  padding-top: 1rem;
  border-top: 1px solid var(--surface-light);
  font-weight: 700;
  color: var(--secondary);
}

.order-qty-select {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin: 1.5rem 0;
}

.order-qty-select button {
  background: var(--surface-light);
  border: none;
  color: var(--primary-light);
  width: 32px;
  height: 32px;
  border-radius: 50%;
  font-size: 1.2rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.order-qty-select button:hover {
  background: var(--primary-light);
  color: var(--on-primary);
}

.order-qty-select span {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--on-surface);
}

.main-btn {
  width: 100%;
  padding: 1rem;
  background: var(--primary-light);
  color: var(--on-primary);
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.main-btn:hover {
  background: var(--secondary);
}
</style> 