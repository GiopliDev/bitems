<template>
  <div class="item-card" @click="goToDetail">
    <div class="item-img">
      <img 
        :src="`/bitems/frontend/UploadedImages/${item.image}`" 
        :alt="item.art_titolo"
      />
    </div>
    <div class="item-body">
      <div class="item-title">{{ item.art_titolo }}</div>
      <div class="item-meta">
        <div class="item-badges">
          <span class="game-badge">{{ item.game_name }}</span>
          <span class="category-badge">{{ item.category_name }}</span>
        </div>
        <span class="item-price">{{ item.art_prezzoUnitario }}€</span>
      </div>
      <div class="item-tags" v-if="item.tags && item.tags.length > 0">
        <span v-for="tag in item.tags.slice(0, 4)" :key="tag" class="item-tag">#{{ tag }}</span>
        <span v-if="item.tags.length > 4" class="more-tags">+{{ item.tags.length - 4 }}</span>
      </div>
      <div class="item-status" :class="{ 'low': item.art_qtaDisp <= 5 }">
        <span v-if="item.art_qtaDisp > 0">{{ item.art_qtaDisp }} disponibili</span>
        <span v-else>Esaurito</span>
      </div>
      <div class="item-footer">
        <div class="seller-info">
          <span class="seller-name">{{ item.seller_name }}</span>
          <span class="seller-rep" :class="getReputationClass(item.seller_rep)">⭐ {{ item.seller_rep }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

interface Item {
  art_id: number
  art_titolo: string
  art_prezzoUnitario: number
  art_qtaDisp: number
  art_descrizione: string
  art_timestamp: string
  art_isPrivato: boolean
  game_name: string
  category_name: string
  seller_name: string
  seller_rep: number
  tags: string[]
  image: string
}

const props = defineProps<{
  item: Item
}>()

const router = useRouter()

function goToDetail() {
  router.push(`/itemDetail?id=${props.item.art_id}`)
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
  width: 100%;
  display: flex;
  flex-direction: column;
  border: 1.5px solid var(--surface-light);
  transition: box-shadow 0.18s, border-color 0.18s, transform 0.18s;
  position: relative;
  cursor: pointer;
  overflow: hidden;
}

.item-card:hover {
  box-shadow: 0 6px 32px 0 rgba(60, 30, 90, 0.13);
  border-color: var(--primary-light);
  transform: translateY(-3px) scale(1.012);
}

.item-img {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 180px;
  border-radius: 16px 16px 0 0;
  overflow: hidden;
  background: var(--surface-light);
}

.item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px 16px 0 0;
}

.item-body {
  padding: 1.2rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}

.item-title {
  font-weight: 700;
  color: var(--on-surface);
  font-size: 1.18rem;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 0.1rem;
  letter-spacing: 0.01em;
}

.item-meta {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  margin-bottom: 0.2rem;
}

.item-badges {
  display: flex;
  gap: 0.8rem;
  align-items: center;
  flex-wrap: wrap;
}

.game-badge {
  background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
  color: white;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 0.4rem 1rem;
  border-radius: 8px;
  letter-spacing: 0.01em;
  min-width: 120px;
  text-align: center;
}

.category-badge {
  background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
  color: white;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 0.4rem 1rem;
  border-radius: 8px;
  letter-spacing: 0.01em;
  min-width: 120px;
  text-align: center;
}

.item-price {
  font-weight: 700;
  color: var(--secondary);
  font-size: 1.2rem;
  text-align: right;
  margin-top: 0.2rem;
}

.item-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.3rem;
  margin: 0.5rem 0;
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

.more-tags {
  font-size: 0.93rem;
  color: var(--on-surface);
  opacity: 0.6;
  padding: 0.08rem 0.6rem;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--on-surface);
  font-size: 0.98rem;
  margin-top: 0.5rem;
  padding-top: 0.8rem;
  border-top: 1px solid var(--surface-light);
}

.seller-info {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

.seller-name {
  color: var(--primary-light);
  font-weight: 600;
  font-size: 1rem;
}

.seller-rep {
  font-weight: 700;
  padding: 0.2rem 0.6rem;
  border-radius: 6px;
  font-size: 0.9rem;
  min-width: 80px;
  text-align: center;
}

.rep-excellent {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
}

.rep-good {
  background: linear-gradient(135deg, #8BC34A 0%, #558B2F 100%);
  color: white;
}

.rep-average {
  background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
  color: #18181c;
}

.rep-bad {
  background: linear-gradient(135deg, #FF5722 0%, #D84315 100%);
  color: white;
}

.item-status {
  font-size: 0.98rem;
  color: var(--success);
  text-align: right;
  margin-top: 0.5rem;
}

.item-status.low {
  color: var(--warning);
}
</style> 