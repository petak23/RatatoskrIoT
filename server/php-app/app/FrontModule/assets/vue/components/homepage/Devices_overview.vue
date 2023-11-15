<script>
import { onMounted, ref } from 'vue'
import MainService from '../../services/MainService'

export default {
	setup () {
		const items = ref(null)

		onMounted(()=> {
			getDevices();
		}) 

		const getDevices = () => {
			MainService.getDevices()
				.then(response => {
					items.value = response.data
				})
				.catch((error) => {
					//console.log(odkaz);
					console.log(error);
				});
		}
	
		return { items }
	}
}
</script>

<template>
	<div 
		v-if="items != null"
		v-for="item in items" :key="item.id" class="col"
	>
		<div class="card h-100 text-bg-dark border-warning">
			<h5 class="card-header">
				{{ item.name }}
			</h5>
			<div class="card-body">
				<p class="card-text"><small>{{ item.decs }}</small></p>
				<h6 class="card-title">Posledné hodnoty zo senzorov:</h6>
				<ul class="list-group list-group-flush">
					<li 
						v-for="sen in item.sensors"
						:key="sen.id"
						class="list-group-item text-bg-dark"
					>
						{{ sen.last_out_value.toFixed(2) }} {{ sen.value_unit }}
					</li>
				</ul>
				<div class="d-flex justify-content-end border-top border-secondary pt-2">
					<a :href="'device/show/' + item.id" class="btn btn-outline-info">
						Viac info <i class="fa-solid fa-angles-right"></i>
					</a>
				</div>
			</div>
		</div>
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