<script>
import { defineComponent } from 'vue'
import CategoryIcon from '../components/CategoryIcon.vue'

const categories = [
  {
    label: 'Cosmetica',
    emoji: '😎',
    color: '#ffb300',
    to: { path: '/catalogo', query: { itemType: 'cosmetica' } }
  },
  {
    label: 'Ingame Items',
    emoji: '🗡️',
    color: '#90caf9',
    to: { path: '/catalogo', query: { itemType: 'ingame' } }
  },
  {
    label: 'Boosting',
    emoji: '🔥',
    color: '#ff7043',
    to: { path: '/catalogo', query: { itemType: 'boosting' } }
  },
  {
    label: 'Trending Items',
    emoji: '⭐',
    color: '#ffd600',
    to: { path: '/catalogo', query: { itemType: 'trending' } }
  },
  {
    label: 'Placeholder',
    emoji: 'X',
    color: '#90caf9',
    to: { path: '/catalogo', query: { itemType: 'ingame' } }
  },
  {
    label: 'Placeholder',
    emoji: 'X',
    color: '#ffb300',
    to: { path: '/catalogo', query: { itemType: 'cosmetica' } }
  }
]

export default defineComponent({
  name: 'HomeView',
  components: { CategoryIcon },
  setup() {
    return { categories }
  }
})
</script>

<template>
  <main>
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">
          Benvenuto nel mondo degli <span class="highlight">Oggetti Virtuali</span> di
          <span class="highlight-secondary">BitItems</span>
        </h1>
        <p class="hero-description">
          Esplora la nostra collezione esclusiva di oggetti digitali per i tuoi giochi preferiti. 
          Dai skin rari agli oggetti leggendari, tutto ciò che serve per migliorare la tua esperienza di gioco.
        </p>
        <div class="hero-buttons">
          <router-link to="/catalogo" class="btn btn-primary">Esplora Catalogo</router-link>
          <router-link to="/chi-siamo" class="btn btn-outline">Chi Siamo</router-link>
        </div>
      </div>

      <div class="hero-image">
        <CategoryIcon
          v-for="(cat, idx) in categories"
          :key="idx"
          :emoji="cat.emoji"
          :color="cat.color"
          :label="cat.label"
          :to="cat.to"
        />
      </div>
    </section>
  </main>
</template>

<style scoped>
main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

/* Hero Section */
.hero {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4rem 0;
  gap: 2rem;
}

.hero-content {
  flex: 1;
  max-width: 600px;
}

.hero-title {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.hero-title span.highlight {
  color: var(--primary-light);
}

.hero-title span.highlight-secondary {
  color: var(--secondary);
}

.hero-description {
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 2rem;
  line-height: 1.6;
  font-size: 1.1rem;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary {
  background-color: var(--primary);
  color: var(--on-primary);
  border: none;
}

.btn-primary:hover {
  background-color: var(--primary-light);
  box-shadow: 0 2px 8px rgba(187, 134, 252, 0.4);
  transform: translateY(-2px);
}

.btn-outline {
  background-color: transparent;
  color: var(--secondary);
  border: 2px solid var(--secondary);
}

.btn-outline:hover {
  background-color: rgba(3, 218, 198, 0.1);
  box-shadow: 0 2px 8px rgba(3, 218, 198, 0.4);
  transform: translateY(-2px);
}

.hero-image {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  max-width: 500px;
}

.section-title {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: var(--on-background);
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    text-align: center;
    padding: 2rem 0;
  }

  .hero-content {
    max-width: 100%;
  }

  .hero-buttons {
    justify-content: center;
  }

  .hero-image {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
