<template>
  <div id="app">
    <Navbar @show-login="showLogin = true" @show-register="showRegister = true" />
    <router-view />
    <LoginPopup
      v-if="showLogin"
      @close="showLogin = false"
      @switchToRegister="switchToRegister"
    />
    <RegisterPopup
      v-if="showRegister"
      @close="showRegister = false"
      @switchToLogin="switchToLogin"
    />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
import LoginPopup from './components/LoginPopup.vue'
import RegisterPopup from './components/RegisterPopup.vue'

export default {
  name: "App",
  components: {
    Navbar,
    LoginPopup,
    RegisterPopup
  },
  data() {
    return {
      showLogin: false,
      showRegister: false
    }
  },
  methods: {
    switchToRegister() {
      this.showLogin = false;
      this.showRegister = true;
    },
    switchToLogin() {
      this.showRegister = false;
      this.showLogin = true;
    }
  }
}
</script>

<style>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
