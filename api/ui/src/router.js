import Vue from 'vue'
import VueRouter from 'vue-router'

import WelcomePage from './components/welcome/welcome.vue'
import DashboardPage from './components/dashboard/dashboard.vue'
import ScratchPage from './components/auth/scratch.vue'
import RecommendPage from './components/auth/recommend.vue'
import BuildProject from './components/auth/build_index.vue'

Vue.use(VueRouter)

const routes = [
  { path: '/', component: WelcomePage },
  { path: '/scratch', component: ScratchPage },
  { path: '/recommend', component: RecommendPage },
  { path: '/dashboard', component: DashboardPage },
  { path: '/completion/:id', component: BuildProject },
]

export default new VueRouter({mode: 'history', routes})