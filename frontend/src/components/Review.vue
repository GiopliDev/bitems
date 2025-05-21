<template>
  <div class="review">
    <div class="review-header">
      <div class="review-user-info">
        <span class="review-user">{{ review.ute_username }}</span>
        <span class="review-date">{{ formatDate(review.rec_timestamp) }}</span>
      </div>
      <span class="review-rating" :class="{ positive: review.rec_voto, negative: !review.rec_voto }">
        {{ review.rec_voto ? '👍 Positiva' : '👎 Negativa' }}
      </span>
    </div>
    <div class="review-body">{{ review.rec_dex }}</div>
  </div>
</template>

<script setup lang="ts">
interface Review {
  rec_id: number
  rec_art_id: number
  rec_ute_id: number
  rec_voto: number
  rec_dex: string
  ute_username: string
  rec_timestamp: string
}

defineProps<{
  review: Review
}>()

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
</style> 