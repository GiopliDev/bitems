<template>
  <div class="catalogo-layout">
    <div class="catalogo-main">
      <h2>Catalogo Principale:</h2>
      <div class="catalogo-cards">
        <ItemCard v-for="item in items" :key="item.id" :item="item" />
      </div>
    </div>
    <FilterBar />
  </div>
</template>

<script setup>
import ItemCard from '../components/ItemCard.vue'
import FilterBar from '../components/FilterBar.vue'

const items = [
  {
    id: 1,
    gameName: 'Valorant',
    itemName: 'Skin Vandal Prime',
    tags: ['skin', 'vandal', 'prime'],
    qty: 1,
    price: 19.99,
    user: 'AceShooter99'
  },
  {
    id: 2,
    gameName: 'League of Legends',
    itemName: 'Account Gold IV',
    tags: ['account', 'gold', 'lol'],
    qty: 1,
    price: 35.00,
    user: 'SummonerPro'
  },
  {
    id: 3,
    gameName: 'CS:GO',
    itemName: 'AWP | Dragon Lore',
    tags: ['awp', 'dragonlore', 'csgo'],
    qty: 1,
    price: 1500.00,
    user: 'SniperKing'
  }
]
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
  background: #111;
  border-radius: 12px;
  padding: 2rem 2rem 2rem 2rem;
  min-width: 0;
}
.catalogo-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-top: 1.5rem;
}
h2 {
  color: #fff;
  margin-bottom: 1.5rem;
}
</style> 