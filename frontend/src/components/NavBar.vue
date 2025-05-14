<template>
  <header>
    <div class="nav-container">
      <a href="../" class="logo">
        <div class="logo-icon">B</div>
        Bit<span>Items</span>
      </a>
      <nav class="nav-links">
        <a href="#" class="nav-link">Home</a>
        <a href="#" class="nav-link">Giochi</a>
        <a href="#" class="nav-link">Oggetti Virtuali</a>
        <a href="#" class="nav-link">Top Vendite</a>
      </nav>
      <div class="auth-links">
        <a href="#" class="auth-btn login-btn" @click.prevent="$emit('show-login')">Accedi</a>
        <a href="#" class="auth-btn register-btn" @click.prevent="$emit('show-register')">Registrati</a>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Navbar'
}
</script>

<style scoped>
header {
  background-color: var(--surface);
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.logo {
  font-weight: bold;
  font-size: 1.5rem;
  color: var(--primary-light);
  text-decoration: none;
  display: flex;
  align-items: center;
}

.logo span {
  color: var(--secondary);
}

.logo-icon {
  width: 40px;
  height: 40px;
  margin-right: 10px;
  background-color: var(--primary-light);
  border-radius: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: var(--on-surface);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
  position: relative;
}

.nav-link:hover {
  color: var(--primary-light);
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background-color: var(--primary-light);
  transition: width 0.3s;
}

.nav-link:hover::after {
  width: 100%;
}

.auth-links {
  display: flex;
  gap: 1rem;
}

.auth-btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s;
}

.login-btn {
  background-color: transparent;
  color: var(--on-surface);
  border: 1px solid var(--primary-light);
}

.login-btn:hover {
  background-color: rgba(187, 134, 252, 0.1);
}

.register-btn {
  background-color: var(--primary);
  color: var(--on-primary);
  border: none;
}

.register-btn:hover {
  background-color: var(--primary-light);
  box-shadow: 0 2px 8px rgba(187, 134, 252, 0.4);
}

@media (max-width: 768px) {
  .nav-links, .auth-links {
    display: none;
  }
}
</style>
