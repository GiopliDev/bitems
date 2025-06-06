<template>
  <div class="item-detail-layout" v-if="item">
    <div class="item-main">
      <div class="item-header">
        <h2 class="item-title">{{ item.art_titolo }}</h2>
        <div v-if="item.isOwner" class="item-actions">
          <button class="edit-btn" @click="showEditPopup = true">
            <i class="fas fa-edit"></i> Modifica
          </button>
          <button class="delete-btn" @click="confirmDelete">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
      <div class="item-slider" v-if="item.images && item.images.length > 0">
        <button class="slider-btn" @click="prevImage">&#8592;</button>
        <img 
          :src="currentImage" 
          alt="item image" 
          class="slider-img" 
          @click="showFullImage = true"
          style="cursor: pointer;"
        />
        <button class="slider-btn" @click="nextImage">&#8594;</button>
      </div>
      <div v-else class="no-image">
        <span class="no-image-text">No image</span>
      </div>
      <div class="slider-dots" v-if="item.images && item.images.length > 0">
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
          👍 {{ item.likes || 0 }}
        </span>
        <span class="dislike">
          👎 {{ item.dislikes || 0 }}
        </span>
            <button 
          class="bookmark-btn" 
          :class="{ 'is-bookmarked': isBookmarked }"
          @click="addBookmark"
            >
          <i class="fas" :class="isBookmarked ? 'fa-bookmark' : 'fa-bookmark-o'"></i>
          {{ isBookmarked ? 'Rimuovi dai preferiti' : 'Aggiungi ai preferiti' }}
            </button>
          </div>
      <div class="item-reviews">
        <h3>Recensioni</h3>
        <AddReview 
          v-if="canReview" 
          :itemId="item?.art_id"
          @review-submitted="handleReviewSubmitted"
        />
        <div v-if="item.recensioni && item.recensioni.length > 0" class="reviews-list">
          <Review 
            v-for="review in item.recensioni" 
            :key="review.rec_id" 
            :review="review"
          />
        </div>
        <div v-else class="no-reviews">
          <p>Nessuna recensione disponibile</p>
        </div>
      </div>
    </div>
    <div class="item-side">
      <div class="user-box">
        <div class="user-avatar">
          <img 
            :src="`/bitems/frontend/UploadedImages/${item.seller_img_url}`" 
            :alt="item.seller_name"
            class="avatar-img"
          />
        </div>
        <div class="user-info">
          <router-link 
            :to="{ path: '/profile', query: { id: item.art_ute_id }}" 
            class="user-name"
          >
            {{ item.seller_name }}
          </router-link>
          <div class="user-rep">
            <span class="rep-label">Reputazione:</span>
            <span class="rep-value" :class="getReputationClass(item.seller_rep)">{{ item.seller_rep }}</span>
          </div>
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
        <button v-if="isLoggedIn" class="main-btn" @click="showPaymentPopup = true">Procedi con l'ordine</button>
        <button v-else class="main-btn" @click="showLoginAlert">Procedi con l'ordine</button>
      </div>
    </div>
  </div>
  <PaymentPopup 
    v-if="showPaymentPopup" 
    :itemId="item?.art_id"
    :quantity="orderQty"
    :price="item?.art_prezzoUnitario"
    @close="showPaymentPopup = false"
    @payment-success="handlePaymentSuccess"
  />
  <EditItemPopup 
    v-if="showEditPopup" 
    :item="item"
    @close="showEditPopup = false"
    @item-updated="handleItemUpdate"
  />
  <CustomAlert
    v-if="showAlert"
    :show="showAlert"
    :title="alertTitle"
    :subtitle="alertSubtitle"
    @close="closeAlert"
  />
  <ImagePopup
    v-if="showFullImage"
    :show="showFullImage"
    :imageUrl="currentImage"
    @close="showFullImage = false"
  />
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from '@/config/axios'
import PaymentPopup from '../components/PaymentPopup.vue'
import CustomAlert from '../components/CustomAlert.vue'
import EditItemPopup from '../components/EditItemPopup.vue'
import Review from '../components/Review.vue'
import AddReview from '../components/AddReview.vue'
import ImagePopup from '../components/ImagePopup.vue'

//interface per comodita e mantenere gli stessi dati tra php e vue

interface Review {
  rec_id: number
  rec_art_id: number
  rec_ute_id: number
  rec_voto: string
  rec_dex: string
  ute_username: string
  rec_timestamp: string
}

interface Item {
  art_id: number
  art_titolo: string
  art_qtaDisp: number
  art_prezzoUnitario: number
  art_descrizione: string
  art_timestamp: string
  art_status: string
  art_ute_id: number
  seller_name: string
  seller_rep: number
  seller_img_id: number
  seller_img_url: string
  recensioni: Review[]
  tags?: string[]
  images?: string[]
  isOwner: boolean
  likes?: number
  dislikes?: number
  art_isPrivato?: boolean
}

// Gestione errori tipizzata
interface ApiError {
  response?: {
    status?: number
  }
}

const route = useRoute()
const router = useRouter()
const item = ref<Item | null>(null)
const imageIndex = ref(0)
const orderQty = ref(1)
const currentImage = ref('')
const showPaymentPopup = ref(false)
const showEditPopup = ref(false)
const showFullImage = ref(false)
const isBookmarked = ref(false)

// Alert state
const showAlert = ref(false)
const alertTitle = ref('')
const alertSubtitle = ref('')

// Check if user is logged in
const isLoggedIn = computed(() => {
  return localStorage.getItem('user') !== null
})

const reviewRating = ref<number | null>(null)
const reviewDescription = ref('')
const canReview = ref(false)

onMounted(async () => {
  try {
    // Get item details
    const response = await axios.get(`/bitems/frontend/backend/getItem.php?id=${route.query.id}`)
    if (response.data.success) {
      item.value = response.data.item
      
      // Calcola like e dislike dalle recensioni
      if (item.value?.recensioni) {
        item.value.likes = item.value.recensioni.filter(review => review.rec_voto === "1").length
        item.value.dislikes = item.value.recensioni.filter(review => review.rec_voto === "0").length
      }
      
      // Set current image if there are images
      if (item.value?.images && item.value.images.length > 0) {
        currentImage.value = `/bitems/frontend/UploadedImages/${item.value.images[0]}`
      } else {
        // If no images found, use default image
        currentImage.value = '/bitems/frontend/UploadedImages/default.jpg'
      }

      // Check if user can review
      try {
        const reviewCheck = await axios.get(`/bitems/frontend/backend/checkCanReview.php?itemId=${route.query.id}`)
        canReview.value = reviewCheck.data.canReview
      } catch (reviewError) {
        console.error('Errore nel controllo recensioni:', reviewError)
        canReview.value = false
      }

      // Check if item is bookmarked
      if (isLoggedIn.value) {
        try {
          const bookmarkCheck = await axios.post('/bitems/frontend/backend/bookmarks.php', {
            action: 'checkBookmark',
            id_articolo: item.value?.art_id
          })
          isBookmarked.value = bookmarkCheck.data.isBookmarked
        } catch (bookmarkError) {
          console.error('Errore nel controllo bookmark:', bookmarkError)
          isBookmarked.value = false
        }
      }
    } else {
      throw new Error('Dati articolo non validi')
    }
  } catch (error: unknown) {
    console.error('Errore nel recupero dei dati:', error)
    const apiError = error as ApiError
    if (apiError.response?.status === 404) {
      showCustomAlert('Errore', 'Articolo non trovato')
    } else if (apiError.response?.status === 401) {
      showCustomAlert('Errore', 'Accesso non autorizzato')
    } else {
      showCustomAlert('Errore', 'Impossibile caricare i dettagli dell\'articolo')
    }
  }
})

// Funzione per gestire le immagini
function prevImage() {
  if (!item.value?.images?.length) return
  imageIndex.value = (imageIndex.value - 1 + item.value.images.length) % item.value.images.length
  currentImage.value = `/bitems/frontend/UploadedImages/${item.value.images[imageIndex.value]}`
}

function nextImage() {
  if (!item.value?.images?.length) return
  imageIndex.value = (imageIndex.value + 1) % item.value.images.length
  currentImage.value = `/bitems/frontend/UploadedImages/${item.value.images[imageIndex.value]}`
}

function goToImage(idx: number) {
  if (!item.value?.images?.length) return
  imageIndex.value = idx
  currentImage.value = `/bitems/frontend/UploadedImages/${item.value.images[imageIndex.value]}`
}

function showCustomAlert(title: string, subtitle: string) {
  alertTitle.value = title
  alertSubtitle.value = subtitle
  showAlert.value = true
}

function closeAlert() {
  showAlert.value = false
}

async function addBookmark() {
  if (!isLoggedIn.value) {
    showLoginAlert()
    return
  }

  try {
    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: isBookmarked.value ? 'removeBookmark' : 'addBookmark',
      id_articolo: item.value?.art_id
    })

    if (response.data.success) {
      isBookmarked.value = !isBookmarked.value
      showCustomAlert(
        isBookmarked.value ? 'Bookmark aggiunto' : 'Bookmark rimosso',
        isBookmarked.value ? 'L\'articolo è stato aggiunto ai preferiti' : 'L\'articolo è stato rimosso dai preferiti'
      )
    }
  } catch (error) {
    console.error('Error managing bookmark:', error)
    showCustomAlert('Errore', 'Impossibile gestire il bookmark')
  }
}

function showLoginAlert() {
  showCustomAlert(
    'Accesso richiesto',
    'Per procedere con l\'ordine è necessario effettuare l\'accesso. Verrai reindirizzato alla pagina di login.'
  )
  setTimeout(() => {
    router.push('/login')
  }, 2000)
}

function getReputationClass(rep: number): string {
  if (rep >= 100) return 'rep-excellent'
  if (rep >= 50) return 'rep-good'
  if (rep >= 20) return 'rep-average'
  return 'rep-bad'
}

async function confirmDelete() {
  if (confirm('Sei sicuro di voler eliminare questo articolo?')) {
    try {
      const response = await axios.delete(`/bitems/frontend/backend/deleteItem.php?id=${item.value?.art_id}`)
      if (response.data.success) {
        router.push('/catalogo')
      } else {
        showCustomAlert('Errore', 'Impossibile eliminare l\'articolo')
      }
    } catch (error) {
      console.error('Error deleting item:', error)
      showCustomAlert('Errore', 'Si è verificato un errore durante l\'eliminazione')
    }
  }
}

function handleItemUpdate(updatedItem: Item) {
  item.value = updatedItem
  showEditPopup.value = false
}

async function handleReviewSubmitted() {
      // Reload item data to show new review
      const itemResponse = await axios.get(`bitems/frontend/backend/getItem.php?id=${route.query.id}`)
      item.value = itemResponse.data.item
      canReview.value = false
}

function handlePaymentSuccess() {
  showPaymentPopup.value = false
  // Ricarica i dati dell'articolo per aggiornare la quantità disponibile
  loadItemData()
}

async function loadItemData() {
  try {
    const response = await axios.get(`/bitems/frontend/backend/getItem.php?id=${route.query.id}`)
    if (response.data.success) {
      item.value = response.data.item
      
      // Calcola like e dislike dalle recensioni
      if (item.value?.recensioni) {
        item.value.likes = item.value.recensioni.filter(review => review.rec_voto === "1").length
        item.value.dislikes = item.value.recensioni.filter(review => review.rec_voto === "0").length
      }
      
      // Set current image if there are images
      if (item.value?.images && item.value.images.length > 0) {
        currentImage.value = `/bitems/frontend/UploadedImages/${item.value.images[0]}`
      } else {
        // If no images found, use default image
        currentImage.value = '/bitems/frontend/UploadedImages/default.jpg'
      }

      // Check if user can review
      const reviewCheck = await axios.get(`/bitems/frontend/backend/checkCanReview.php?itemId=${route.query.id}`)
      canReview.value = reviewCheck.data.canReview

      // Check if item is bookmarked
      if (isLoggedIn.value) {
        const bookmarkCheck = await axios.post('/bitems/frontend/backend/bookmarks.php', {
          action: 'checkBookmark',
          id_articolo: item.value?.art_id
        })
        isBookmarked.value = bookmarkCheck.data.isBookmarked
      }
    }
  } catch (error) {
    console.error('Error loading item data:', error)
    showCustomAlert('Errore', 'Impossibile caricare i dettagli dell\'articolo')
  }
}

function formatDate(timestamp: string) {
  const date = new Date(timestamp)
  return date.toLocaleDateString('it-IT', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
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

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.2rem;
  width: 100%;
}

.item-actions {
  display: flex;
  gap: 1rem;
}

.edit-btn, .delete-btn {
  background: var(--surface-light);
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 0.8rem 1.2rem;
  border-radius: 8px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.edit-btn {
  color: var(--primary-light);
  border: 2px solid var(--primary-light);
}

.delete-btn {
  color: var(--error);
  border: 2px solid var(--error);
}

.edit-btn:hover {
  background: var(--primary-light);
  color: var(--on-primary);
}

.delete-btn:hover {
  background: var(--error);
  color: var(--on-primary);
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
  background: var(--surface);
  border-radius: 12px;
  padding: 1.5rem;
}

.item-reviews h3 {
  color: var(--primary-light);
  font-size: 1.3rem;
  margin-bottom: 1.5rem;
}

.add-review {
  background: var(--surface-light);
  padding: 1.5rem;
  border-radius: 12px;
  margin-bottom: 2rem;
}

.add-review h4 {
  color: var(--primary-light);
  margin-bottom: 1rem;
}

.review-rating {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.rating-btn {
  background: none;
  border: 2px solid var(--secondary);
  color: var(--secondary);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.2s;
}

.rating-btn.active {
  background: var(--secondary);
  color: var(--on-primary);
}

.rating-btn:hover {
  transform: scale(1.1);
}

.review-textarea {
  width: 100%;
  min-height: 100px;
  background: transparent;
  border: 2px solid var(--secondary);
  border-radius: 8px;
  padding: 0.8rem;
  color: var(--on-surface);
  font-size: 1rem;
  margin-bottom: 1rem;
  resize: vertical;
}

.review-textarea:focus {
  outline: none;
  border-color: var(--primary-light);
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.review {
  background: var(--surface-light);
  padding: 1.2rem;
  border-radius: 12px;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.review-user-info {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.review-user {
  font-weight: 600;
  color: var(--primary-light);
}

.review-date {
  font-size: 0.9rem;
  color: var(--on-surface);
  opacity: 0.7;
}

.review-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.8rem;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 500;
}

.review-rating.positive {
  background: var(--secondary);
  color: #18181c;
}

.review-rating.negative {
  background: var(--error);
  color: var(--on-error);
}

.review-body {
  color: var(--on-surface);
  line-height: 1.5;
  font-size: 1rem;
}

.no-reviews {
  text-align: center;
  padding: 2rem;
  color: var(--on-surface);
  opacity: 0.7;
  font-style: italic;
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
  border-radius: 12px;
  padding: 1.2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 12px #0001;
}

.user-avatar {
  width: 48px;
  height: 48px;
  background: var(--surface-light);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.user-name {
  color: var(--primary-light);
  font-weight: 600;
  font-size: 1.1rem;
  text-decoration: none;
  transition: color 0.2s;
}

.user-name:hover {
  color: var(--secondary);
}

.user-rep {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.95rem;
}

.rep-label {
  color: var(--on-surface);
  opacity: 0.8;
}

.rep-value {
  font-weight: 600;
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

.no-image {
  width: 320px;
  height: 180px;
  background: var(--surface-light);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.no-image-text {
  color: var(--on-surface);
  opacity: 0.7;
  font-size: 1.2rem;
  font-weight: 500;
}

.bookmark-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.2rem;
  background: var(--surface-light);
  color: var(--on-surface);
  border: 2px solid var(--primary-light);
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.bookmark-btn:hover {
  background: var(--primary-light);
  color: var(--on-primary);
}

.bookmark-btn.is-bookmarked {
  background: var(--primary-light);
  color: var(--on-primary);
}

.bookmark-btn.is-bookmarked:hover {
  background: var(--surface-light);
  color: var(--on-surface);
}

.bookmark-btn i {
  font-size: 1.1rem;
}
</style> 