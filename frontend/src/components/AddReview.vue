<template>
  <div class="add-review">
    <h4>Aggiungi una recensione</h4>
    <div class="review-rating">
      <button 
        class="rating-btn" 
        :class="{ active: reviewRating === 1 }"
        @click="reviewRating = 1"
      >
        👍
      </button>
      <button 
        class="rating-btn" 
        :class="{ active: reviewRating === 0 }"
        @click="reviewRating = 0"
      >
        👎
      </button>
    </div>
    <textarea 
      v-model="reviewDescription" 
      placeholder="Scrivi la tua recensione..."
      class="review-textarea"
    ></textarea>
    <button 
      class="main-btn" 
      @click="submitReview"
      :disabled="!reviewDescription || reviewRating === null"
    >
      Invia Recensione
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from '@/config/axios'

const props = defineProps<{
  itemId: number
}>()

const emit = defineEmits<{
  (e: 'review-submitted'): void
}>()

const reviewRating = ref<number | null>(null)
const reviewDescription = ref('')

async function submitReview() {
  if (!reviewDescription.value || reviewRating.value === null) return

  try {
    const response = await axios.post('/bitems/frontend/backend/addReview.php', {
      itemId: props.itemId,
      rating: reviewRating.value,
      description: reviewDescription.value
    })

    if (response.data.success) {
      reviewRating.value = null
      reviewDescription.value = ''
      emit('review-submitted')
    }
  } catch (error) {
    console.error('Error submitting review:', error)
  }
}
</script>

<style scoped>
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

.main-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style> 