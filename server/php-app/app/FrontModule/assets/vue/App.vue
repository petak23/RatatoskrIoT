<script setup>
import Units from './components/Units/Units.vue'
import MySettings from './components/MySettings.vue'
import { useMainStore } from './store/main'

const store = useMainStore()
</script>

<template>
	<my-settings />
	<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
		<button class="btn btn-link btn-sm ml-2 mr-2" id="sidebarToggle" href="#">
			<i class="fas fa-bars"></i>
		</button>
		<a class="navbar-brand" :href="store.basePath" :title="store.appName">
			{{ store.appName }}
		</a>
	</nav>
	<div id="layoutSidenav">
		<div id="layoutSidenav_nav">
			<nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
				<div class="sb-sidenav-menu">
					<div class="nav">
						<div class="sb-sidenav-menu-heading">Menu</div>
							<a n:class="nav-link, pt-0, pb-1" n:href=":Inventory:user">Späť na hlavnú stránku</a>
						<div>&nbsp;</div>
					</div>
				</div>
			</nav>
		</div>
		<div id="layoutSidenav_content">
			<main>
				<div  class="container-fluid px-0 mx-0 px-md-2 px-lg-4">
					<div class="row px-2 bg-light">
						<Units />
					</div>
				</div>
			</main>
			<footer class="py-4 bg-light mt-auto">
				<div class="container-fluid">
					<div class="d-flex align-items-center justify-content-between small">
						<div class="text-muted">{{ store.appName }} | 
							PHP {=PHP_VERSION}&nbsp;|&nbsp;
							{ifset $_SERVER['SERVER_SOFTWARE']}Server {$_SERVER['SERVER_SOFTWARE']}{/ifset}
						</div>
						<ul v-if="store.links.length > 0">
							<li v-for="link in store.links">
								<a :href="link.link" target="_blank">
									{{ link.name }}
								</a>
							</li>
						</ul>
					</div>
				</div>
			</footer>
		</div>
	</div>
</template>

<style scoped>

</style>
