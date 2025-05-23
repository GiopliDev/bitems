<template>
  <div class="home">
    <div class="hero">
      <h1>Benvenuto su BItems</h1>
      <p>La piattaforma per acquistare e vendere oggetti dei tuoi giochi preferiti</p>
    </div>

    <div class="games-grid">
      <div class="game-card" @click="goToCatalog('League of Legends', 'Cosmetica')">
        <img src="/bitems/frontend/UploadedImages/lol.jpg" alt="League of Legends">
        <div class="game-info">
          <h3>League of Legends</h3>
          <p>Skin e oggetti cosmetici</p>
        </div>
      </div>

      <div class="game-card" @click="goToCatalog('Valorant', 'Cosmetica')">
        <img src="/bitems/frontend/UploadedImages/valorant.jpg" alt="Valorant">
        <div class="game-info">
          <h3>Valorant</h3>
          <p>Skin e oggetti cosmetici</p>
        </div>
      </div>

      <div class="game-card" @click="goToCatalog('Counter-Strike 2', 'Item')">
        <img src="/bitems/frontend/UploadedImages/cs2.jpg" alt="Counter-Strike 2">
        <div class="game-info">
          <h3>Counter-Strike 2</h3>
          <p>Armi e oggetti di gioco</p>
        </div>
      </div>

      <div class="game-card" @click="goToCatalog('Fortnite', 'Cosmetica')">
        <img src="/bitems/frontend/UploadedImages/fortnite.jpg" alt="Fortnite">
        <div class="game-info">
          <h3>Fortnite</h3>
          <p>Skin e oggetti cosmetici</p>
        </div>
      </div>

      <div class="game-card" @click="goToCatalog('Rocket League', 'Item')">
        <img src="/bitems/frontend/UploadedImages/rocketleague.jpg" alt="Rocket League">
        <div class="game-info">
          <h3>Rocket League</h3>
          <p>Auto e oggetti di gioco</p>
        </div>
      </div>

      <div class="game-card" @click="goToCatalog('Apex Legends', 'Account')">
        <img src="/bitems/frontend/UploadedImages/apex.jpg" alt="Apex Legends">
        <div class="game-info">
          <h3>Apex Legends</h3>
          <p>Account e oggetti di gioco</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

function goToCatalog(game: string, category: string) {
  router.push({
    path: '/catalogo',
    query: { 
      game: game,
      category: category,
      minPrice: '0',
      maxPrice: '2000',
      onlyAvailable: 'false',
      tags: ''
    }
  })
}
</script>

<style scoped>
.home {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.hero {
  text-align: center;
  margin-bottom: 3rem;
}

.hero h1 {
  font-size: 2.5rem;
  color: var(--primary);
  margin-bottom: 1rem;
}

.hero p {
  font-size: 1.2rem;
  color: var(--on-surface);
  opacity: 0.8;
}

.games-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  padding: 1rem;
}

.game-card {
  background: var(--surface);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
  border: 1px solid var(--surface-light);
}

.game-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  border-color: var(--primary-light);
}

.game-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.game-info {
  padding: 1.5rem;
}

.game-info h3 {
  color: var(--on-surface);
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
}

.game-info p {
  color: var(--on-surface);
  opacity: 0.7;
  font-size: 1rem;
}

@media (max-width: 768px) {
  .games-grid {
    grid-template-columns: 1fr;
  }
  
  .hero h1 {
    font-size: 2rem;
  }
  
  .hero p {
    font-size: 1rem;
  }
}
</style> 