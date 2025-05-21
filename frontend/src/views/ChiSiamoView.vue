<template>
  <div class="chi-siamo">
    <h1>Chi Siamo</h1>
    <div class="content">
      <p>Benvenuti su BitItems, la piattaforma dedicata al trading di oggetti virtuali per giochi.</p>
      <p>La nostra missione è fornire un ambiente sicuro e affidabile per gli appassionati di gaming che desiderano scambiare oggetti virtuali.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
// Component logic here
</script>

<style scoped>
.chi-siamo {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: var(--primary-light);
  margin-bottom: 2rem;
  text-align: center;
}

.content {
  background: var(--surface);
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 2px 12px 0 rgba(60, 30, 90, 0.08);
}

p {
  margin-bottom: 1rem;
  line-height: 1.6;
  color: var(--on-surface);
}
</style> 