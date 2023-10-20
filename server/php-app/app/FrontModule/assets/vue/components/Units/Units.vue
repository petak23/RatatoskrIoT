<script>
import { defineComponent } from 'vue'
import axios from 'axios'

export default defineComponent({
	setup() {
		return {}
	},
	data() {
		return {
			items: null,
		}
	},
	methods: {
		getUnits() {
			//let odkaz = this.$store.state.apiPath + 'slider/getall/1'
			let odkaz = "http://localhost/~petak23/RatatoskrIoT/server/php-app/api/units"
			axios.get(odkaz)
				.then(response => {
					//console.log(response.data)
					this.items = response.data
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		},
	},
	mounted() {
		this.getUnits();
	}
})
</script>

<template>
	<div class="h1">
		<h1>Jednotky</h1>
	</div>
	<div v-if="items != null" class="table">
		<table>
			<tr>
				<th>Id</th>
				<th>Meno</th>
			</tr>
			<tr v-for="(id, unit) in items" :key="id">
				<td>{{ id }}</td>
				<td>{{ unit }}</td>
			</tr>
		</table>
	</div>
</template>

<style lang="scss" scoped>
	.h1{ margin-left: 3rem;}
	.table{
		margin-left: 4rem;
		margin-top: 1rem;
	}
</style>