<script>
import { onMounted, ref } from 'vue'
import { useMainStore } from '../store/main'
import axios from 'axios'

export default {
	setup () {

		const store = useMainStore()

		const items = ref(null)

		onMounted(()=> {
			getDevices();
		}) 

		const getDevices = () => {
			let odkaz = store.apiPath + 'devices'
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
		<h1>Zariadenia</h1>
	</div>
	<div v-if="items != null" class="col-12 table">
		<table>
			<tr>
				<th>Id</th>
				<th>Meno<br /><small>Popis</small></th>
				<th>Senzory</th>
			</tr>
			<tr v-for="item in items" :key="item.id">
				<td>{{ item.id }}</td>
				<td>
					{{ item.name }}<br />
					<small>{{ item.decs }}</small>
				</td>
				<td>
					<div v-for="sen in item.sensors" :key="sen.id">
						{{ sen.name }}({{ sen.last_out_value }})
					</div>
				</td>
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