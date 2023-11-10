import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import DevicesView from '../views/DevicesView.vue'
import UnitsView from '../views/UnitsView.vue'
import { useMainStore } from '../store/main'

const routes = [
	{
		path: '/',
		name: 'Domov',
		component: HomeView
	},
	{
		path: '/devices',
		name: 'Zariadenia',
		component: DevicesView
	},
	{
		path: '/units',
		name: 'Jednotky',
		component: UnitsView
		// route level code-splitting
		// this generates a separate chunk (About.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		//component: () => import('../views/UnitsView.vue')
	}
]

const basePath = document.getElementById('app').dataset.basePath

const router = createRouter({
	history: createWebHistory(basePath.substring(1)+"/front/"),
	routes
})

router.beforeEach((to) => {
	const store = useMainStore()
})

export default router
