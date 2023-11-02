<script>
import { onMounted, ref } from 'vue'
import axios from 'axios'

export default {
	setup () {

		const items = ref(null)

		onMounted(()=> {
			getUnits();
		}) 

		const getUnits = () => {
			//let odkaz = this.$store.state.apiPath + 'units'
			let odkaz = "http://localhost/~petak23/RatatoskrIoT/server/php-app/api/units"
			axios.get(odkaz)
				.then(response => {
					//console.log(response.data)
					items.value = response.data
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		}
	
		return { items }
	}
}
</script>

<template>
	<div class="col-12 h1">
		<h1>Jednotky</h1>
	</div>
	<div v-if="items != null" class="col-12 table">
		<table>
			<tr>
				<th>Id</th>
				<td v-for="(id) in items" :key="id">{{ id }}</td>
			</tr>
			<tr>
				<th>Meno</th>
				<td v-for="(id, unit) in items" :key="id">{{ unit }}</td>
			</tr>
		</table>
	</div>
</template>


<style lang="scss" scoped>
	.table{
		margin-top: 1rem;

		tr {
			border: 1px solid #999;
		}

		th {
			border-right: 2px solid #999;
		}

		td {
			border-right: 1px solid #aaa;
		}
	}
</style>