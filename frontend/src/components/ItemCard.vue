<template>
  <div class="item-card">
    <div class="item-header" :style="{ background: headerColor }">
      {{ item.gameName }}
    </div>
    <div class="item-img">img placeholder</div>
    <div class="item-body">
      <div class="item-title">{{ item.itemName }}</div>
      <div class="item-tags">
        <span v-for="tag in item.tags" :key="tag" class="item-tag">#{{ tag }}</span>
      </div>
      <div class="item-info">
        <span>Qta: {{ item.qty }}</span>
        <span class="item-price">Price: {{ item.price }}$</span>
      </div>
      <div class="item-footer">
        <span class="item-user">By {{ item.user }}</span>
        <span class="item-actions">
          <i class="fa-regular fa-thumbs-up"></i>
          <i class="fa-regular fa-thumbs-down"></i>
        </span>
      </div>
    </div>
    <div class="item-bookmark"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  item: Object
})
const headerColor = 'var(--secondary, #03dac6)'
</script>

<style scoped>
.item-card {
  background: #18181c;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 8px #000a;
  min-width: 260px;
  max-width: 300px;
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  border-bottom: 4px solid var(--primary-light, #bb86fc);
  position: relative;
}
.item-header {
  background: var(--secondary, #03dac6);
  color: #fff;
  font-weight: 700;
  font-size: 1.1rem;
  padding: 0.5rem 1rem;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}
.item-img {
  background: #333;
  color: #bbb;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.item-body {
  background: #bb86fc;
  padding: 0.7rem 1rem 0.7rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
.item-title {
  font-weight: 600;
  color: #18181c;
}
.item-tags {
  margin-bottom: 0.3rem;
}
.item-tag {
  background: #ffd600;
  color: #222;
  border-radius: 8px;
  padding: 0.1rem 0.6rem;
  font-size: 0.9rem;
  margin-right: 0.3rem;
  font-weight: 600;
}
.item-info {
  display: flex;
  justify-content: space-between;
  color: #222;
  font-size: 1rem;
}
.item-price {
  font-weight: 700;
}
.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
  color: #222;
  font-size: 0.95rem;
}
.item-user {
  display: flex;
  align-items: center;
  gap: 0.3rem;
}
.item-actions i {
  margin-left: 0.7rem;
  cursor: pointer;
  font-size: 1.1rem;
  color: #222;
  transition: color 0.2s;
}
.item-actions i:hover {
  color: var(--primary-light, #bb86fc);
}
.item-bookmark {
  position: absolute;
  right: 0;
  bottom: 0;
  width: 32px;
  height: 32px;
  background: var(--secondary, #03dac6);
  border-top-left-radius: 8px;
  clip-path: polygon(100% 0, 0 0, 100% 100%);
}
</style> 