<template>
  <div class="item-card">
    <div class="item-header">
      <span class="game-badge">{{ item.gameName }}</span>
    </div>
    <div class="item-img">
      <img :src="item.img || 'https://via.placeholder.com/320x160?text=Item'" alt="item image" />
    </div>
    <div class="item-body">
      <div class="item-title">{{ item.itemName }}</div>
      <div class="item-tags">
        <span v-for="tag in item.tags" :key="tag" class="item-tag">#{{ tag }}</span>
      </div>
      <div class="item-info">
        <span class="item-qty">Qta: {{ item.qty }}</span>
        <span class="item-price">{{ item.price }} €</span>
      </div>
      <div class="item-footer">
        <span class="item-user"><i class="fa-regular fa-user"></i> {{ item.user }}</span>
        <span class="item-actions">
          <i class="fa-regular fa-thumbs-up"></i>
          <i class="fa-regular fa-thumbs-down"></i>
        </span>
      </div>
    </div>
    <div class="item-bookmark">
      <i class="fa-regular fa-bookmark"></i>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  item: Object
})
</script>

<style scoped>
.item-card {
  background: linear-gradient(135deg, #232336 0%, #18181c 100%);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 6px 32px 0 rgba(60, 30, 90, 0.18), 0 1.5px 6px 0 rgba(0,0,0,0.10);
  min-width: 320px;
  max-width: 370px;
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  border-bottom: 5px solid var(--primary-light, #bb86fc);
  position: relative;
  transition: box-shadow 0.2s, transform 0.2s;
}
.item-card:hover {
  box-shadow: 0 12px 48px 0 rgba(60, 30, 90, 0.28), 0 3px 12px 0 rgba(0,0,0,0.18);
  transform: translateY(-6px) scale(1.025);
}
.item-header {
  padding: 1.1rem 1.5rem 0.5rem 1.5rem;
  display: flex;
  align-items: center;
  background: none;
}
.game-badge {
  background: var(--secondary, #03dac6);
  color: #18181c;
  font-weight: 700;
  font-size: 1.05rem;
  padding: 0.35rem 1.1rem;
  border-radius: 999px;
  letter-spacing: 0.03em;
  box-shadow: 0 2px 8px #03dac633;
}
.item-img {
  background: #232336;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 160px;
  padding: 0.5rem 0;
}
.item-img img {
  width: 90%;
  height: 100%;
  object-fit: cover;
  border-radius: 14px;
  box-shadow: 0 2px 12px #0005;
  border: 2.5px solid var(--surface-light, #2d2d2d);
}
.item-body {
  background: #bebdee;
  padding: 1.2rem 1.5rem 1.2rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  border-bottom-left-radius: 18px;
  border-bottom-right-radius: 18px;
}
.item-title {
  font-weight: 700;
  color: #232336;
  font-size: 1.25rem;
  margin-bottom: 0.2rem;
}
.item-tags {
  margin-bottom: 0.2rem;
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
.item-info {
  display: flex;
  justify-content: space-between;
  color: #232336;
  font-size: 1.08rem;
  font-weight: 500;
}
.item-qty {
  color: #292828;
}
.item-price {
  font-weight: 700;
  color: var(--primary, #9c27b0);
  font-size: 1.13rem;
}
.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.7rem;
  color: #232336;
  font-size: 1.05rem;
}
.item-user {
  display: flex;
  align-items: center;
  gap: 0.3rem;
}
.item-actions i {
  margin-left: 0.7rem;
  cursor: pointer;
  font-size: 1.18rem;
  color: #bbb;
  transition: color 0.2s;
}
.item-actions i:hover {
  color: var(--primary-light, #bb86fc);
}
.item-bookmark {
  position: absolute;
  right: 0.7rem;
  top: 0.7rem;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 50%;
  box-shadow: 0 2px 8px #0002;
  color: var(--primary-light, #bb86fc);
  font-size: 1.3rem;
  z-index: 2;
}
.item-bookmark i {
  color: var(--primary-light, #bb86fc);
}
</style> 