import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/catalogo',
      name: 'catalogo',
      component: () => import('../views/Catalogo.vue'),
    },
    {
      path: '/itemDetail',
      name: 'itemDetail',
      component: () => import('../views/ItemDetail.vue'),
    },
    {
      path: '/chats',
      name: 'chats',
      component: () => import('../views/ChatsView.vue'),
    },
    {
      path: '/addItem',
      name: 'addItem',
      component: () => import('../views/addItemView.vue'),
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
    },
    {
      path: '/chi-siamo',
      name: 'chi-siamo',
      component: () => import('../views/ChiSiamoView.vue'),
    },
    {
      path: '/trending',
      name: 'trending',
      component: () => import('../views/TrendingView.vue'),
    },
    {
      path: '/history',
      name: 'history',
      component: () => import('../views/HistoryView.vue'),
    },
    {
      path: '/bookmarks',
      name: 'bookmarks',
      component: () => import('../views/BookmarksView.vue'),
    },
  ],
})

export default router
