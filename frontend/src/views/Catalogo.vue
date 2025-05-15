<template>
  <div class="catalogo-layout">
    <div class="catalogo-main">
      <h2>Catalogo Oggetti</h2>
      <section class="catalogo-section">
        <h3 class="section-title hot">🔥 Hot Items</h3>
        <div class="catalogo-cards">
          <ItemCard v-for="item in hotItems" :key="item.id" :item="item" />
        </div>
      </section>
      <section class="catalogo-section">
        <h3 class="section-title recent">🕒 Recent Items</h3>
        <div class="catalogo-cards">
          <ItemCard v-for="item in recentItems" :key="item.id" :item="item" />
        </div>
      </section>
      <section class="catalogo-section" v-for="(items, game) in itemsByGame" :key="game">
        <h3 class="section-title">{{ game }}</h3>
        <div class="catalogo-cards">
          <ItemCard v-for="item in items" :key="item.id" :item="item" />
          <router-link :to="{ path: '/catalogo', query: { game: game } }" class="see-more">Vedi tutti</router-link>
        </div>
      </section>
    </div>
    <FilterBar />
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import ItemCard from '../components/ItemCard.vue'
import FilterBar from '../components/FilterBar.vue'
import itemsData from '../jsons/items.json'

// Tipi TypeScript per un item
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

// In futuro: qui potrai sostituire con una fetch API
const allItems = ref<Item[]>(itemsData as Item[])

const hotItems = computed(() => allItems.value.filter(i => i.isHot).slice(0, 4))
const recentItems = computed(() => [...allItems.value].sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()).slice(0, 4))
const itemsByGame = computed(() => {
  const grouped: Record<string, Item[]> = {}
  allItems.value.forEach(item => {
    if (!grouped[item.gameName]) grouped[item.gameName] = []
    grouped[item.gameName].push(item)
  })
  return grouped
})
</script>

<style scoped>
.catalogo-layout {
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 2rem 1rem;
  gap: 2rem;
}
.catalogo-main {
  flex: 1;
  background: #18181c;
  border-radius: 16px;
  padding: 2rem 2rem 2rem 2rem;
  min-width: 0;
  box-shadow: 0 2px 16px #0002;
}
.catalogo-section {
  margin-bottom: 2.5rem;
}
.section-title {
  color: var(--primary-light);
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 1rem;
  letter-spacing: 0.01em;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.section-title.hot {
  color: var(--secondary);
}
.section-title.recent {
  color: var(--primary);
}
.catalogo-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 0.5rem;
}
.see-more {
  display: inline-block;
  margin-left: 1rem;
  color: var(--primary-light);
  font-size: 1rem;
  text-decoration: underline;
  align-self: center;
  transition: color 0.2s;
}
.see-more:hover {
  color: var(--secondary);
}
h2 {
  color: var(--on-background);
  margin-bottom: 2.5rem;
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: 0.01em;
}
</style> 