<script setup>
import MySettings from './components/MySettings.vue'
import LayoutFooter from './components/LayoutFooter.vue'
import { useMainStore } from './store/main'
import { RouterLink, RouterView } from 'vue-router'

const store = useMainStore()
</script>

<template>
	<my-settings />
	<nav class="navbar navbar-expand navbar-dark bg-dark py-0">
		<button class="btn btn-link btn-sm ml-2 mr-2" id="sidebarToggle" href="#">
			<i class="fas fa-bars"></i>
		</button>
		<a class="navbar-brand" :href="store.basePath" :title="store.appName">
			{{ store.appName }}
		</a>
	</nav>
	<div class="container-fluid">
		<div class="row">

			<nav class="col-12 col-md-3 col-lg-2 bg-light pt-2">
				<div class="h6">Menu</div>
				<ul class="nav flex-column">
					<li class="nav-item">
						<a class="nav-link pt-0 pb-1" :href="store.basePath">Späť na hlavnú stránku</a>
					</li>
					<li class="nav-item">
						<RouterLink to="/" class="nav-link">Home</RouterLink>
					</li>
					<li class="nav-item">
						<RouterLink to="/units" class="nav-link">Jednotky</RouterLink>
					</li>
				</ul>
			</nav>
		
			<main class="col-12 col-md-9 col-lg-10">
				<RouterView />
			</main>

		</div>
	</div>
	<layout-footer />
</template>

<style scoped>

</style>
