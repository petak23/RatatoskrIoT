<script setup>
import Devices_overview from '../components/homepage/Devices_overview.vue';
import User_overview from '../components/homepage/User_overview.vue';
</script>

<template>
	<div class="col-12 h1">
		<h1>Prehľad:</h1>
	</div>
	<div class="row row-cols-1 row-cols-md-3 g-4 mb-2">
		<User_overview />
		<Devices_overview />
	</div>
</template>