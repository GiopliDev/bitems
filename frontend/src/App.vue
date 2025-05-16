<template>
  <div id="app">
    <Navbar :user="loggedUser" @show-login="showLogin = true" @show-register="showRegister = true" />
    <div style="display: flex;">
      <UserSidebar v-if="loggedUser" :user="loggedUser" />
      <router-view />
    </div>
    <LoginPopup
      v-if="showLogin"
      @close="showLogin = false"
      @switchToRegister="switchToRegister"
      @login-success="onLoginSuccess"
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
import UserSidebar from './components/UserSidebar.vue'

export default {
  name: "App",
  components: {
    Navbar,
    LoginPopup,
    RegisterPopup,
    UserSidebar
  },
  data() {
    return {
      showLogin: false,
      showRegister: false,
      loggedUser: null
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
    },
    onLoginSuccess(user) {
      this.loggedUser = user;
      this.showLogin = false;
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
