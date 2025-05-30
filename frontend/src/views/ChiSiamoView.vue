<template>
  <div class="about-us">
    <div class="hero-section">
      <h1>Chi Siamo</h1>
      <p class="subtitle">La tua piattaforma di fiducia per il trading di oggetti virtuali</p>
    </div>

    <div class="content-section">
      <div class="section">
        <h2>La Nostra Storia</h2>
        <p>
          BItems nasce dalla passione per i videogiochi e dalla necessità di creare un marketplace 
          sicuro e affidabile per gli appassionati di gaming. La nostra piattaforma è stata 
          progettata per offrire un'esperienza di trading trasparente e sicura, dove gli utenti 
          possono acquistare e vendere oggetti virtuali con la massima tranquillità.
        </p>
      </div>

      <div class="section">
        <h2>La Nostra Missione</h2>
        <div class="mission-cards">
          <div class="mission-card">
            <span class="emoji">🛡️</span>
            <h3>Sicurezza</h3>
            <p>Garantiamo transazioni sicure e protezione dei dati dei nostri utenti</p>
          </div>
          <div class="mission-card">
            <span class="emoji">🤝</span>
            <h3>Affidabilità</h3>
            <p>Creiamo un ambiente di trading affidabile e trasparente</p>
          </div>
          <div class="mission-card">
            <span class="emoji">🎮</span>
            <h3>Passione</h3>
            <p>Condividiamo la passione per il gaming con la nostra community</p>
          </div>
        </div>
      </div>

      <div class="section">
        <h2>Perché Scegliere BItems</h2>
        <div class="features-grid">
          <div class="feature">
            <span class="emoji">🔒</span>
            <h3>Sistema Sicuro</h3>
            <p>Transazioni protette e verifica degli utenti</p>
          </div>
          <div class="feature">
            <span class="emoji">💎</span>
            <h3>Vasta Selezione</h3>
            <p>Oggetti virtuali per i tuoi giochi preferiti</p>
          </div>
          <div class="feature">
            <span class="emoji">⭐</span>
            <h3>Reputazione</h3>
            <p>Sistema di recensioni e feedback</p>
          </div>
          <div class="feature">
            <span class="emoji">💬</span>
            <h3>Supporto</h3>
            <p>Assistenza clienti sempre disponibile</p>
          </div>
        </div>
      </div>

      <div class="section">
        <h2>Contattaci</h2>
        <div class="contact-info">
          <div class="contact-item">
            <span class="emoji">📧</span>
            <p>Email: gabriele.bondoni@gmail.com</p>
          </div>
          <div class="contact-item">
            <span class="emoji">📱</span>
            <p>Telefono: +39 345 878 8641</p>
          </div>
          <div class="contact-item">
            <span class="emoji">📍</span>
            <p>Indirizzo: Via del Mulino 4, Pieve Santo stefano, AR, IT</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Component logic here
</script>

<style scoped>
.about-us {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.hero-section {
  text-align: center;
  margin-bottom: 4rem;
  padding: 3rem 0;
  background: var(--surface);
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h1 {
  font-size: 2.5rem;
  color: var(--primary-light);
  margin-bottom: 1rem;
}

.subtitle {
  font-size: 1.2rem;
  color: var(--on-surface);
  opacity: 0.8;
}

.section {
  margin-bottom: 4rem;
}

h2 {
  font-size: 2rem;
  color: var(--primary-light);
  margin-bottom: 2rem;
  text-align: center;
}

.mission-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.mission-card {
  background: var(--surface);
  padding: 2rem;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.mission-card:hover {
  transform: translateY(-5px);
}

.mission-card .emoji {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  display: block;
}

.mission-card h3 {
  color: var(--primary-light);
  margin-bottom: 1rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.feature {
  background: var(--surface);
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.feature .emoji {
  font-size: 2rem;
  margin-bottom: 1rem;
  display: block;
}

.feature h3 {
  color: var(--primary-light);
  margin-bottom: 0.5rem;
}

.contact-info {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.contact-item {
  background: var(--surface);
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.contact-item .emoji {
  font-size: 1.5rem;
}

p {
  color: var(--on-surface);
  line-height: 1.6;
  margin: 0;
}

@media (max-width: 768px) {
  .about-us {
    padding: 1rem;
  }

  .hero-section {
    padding: 2rem 1rem;
  }

  h1 {
    font-size: 2rem;
  }

  .mission-cards,
  .features-grid,
  .contact-info {
    grid-template-columns: 1fr;
  }
}
</style> 