<script>
import { onMounted, ref } from 'vue'
import { useMainStore } from '../store/main'
import axios from 'axios'
import dayjs from 'dayjs'; //https://day.js.org/docs/en/display/format

export default {
	setup () {

		const store = useMainStore()

		const items = ref(null)

		onMounted(()=> {
			getDevices();
		})

		
		const format_date = (value) => {
			const date = dayjs(value);
			// Then specify how you want your dates to be formatted
			return date.format('D.M.YYYY HH:mm:ss');
		}

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
	
		return { items, format_date }
	}
}
</script>

<template>
	<div v-if="items != null" v-for="item in items" :key="item.id">
		<div class="row px-2 text-secondary" >
			<div class="col-4 col-md-2 ">Zariadenie</div>
			<div class="col-4  col-md-2">Prvé prihlásenie</div>
			<div class="col-4  col-md-2">Posledné prihlásenie</div>
			<div class="col-12 col-md-1 ">Popis</div>
		</div>

		<div class="row my-2 px-2 bg-primary text-white">
			<div class="col-4 col-md-2 ">
				<b>
					<a :href="'device/show/' + item.id" class="text-white">
						{{ item.name }}
					</a>
				</b>
				<a 
					v-if="item.problem_mark"
					href="#"
					data-toggle="tooltip"
					data-placement="top"
					:title="'Zařízení má problém s přihlášením. Poslední neúspěšné přihlášení: ' + item.last_bad_login + '.'"
				>
					<i class="text-warning fas fa-exclamation-triangle"></i>
				</a>
				<a 
					v-if="item.config_data != null"
					href="#" 
					data-toggle="tooltip" 
					data-placement="top" 
					title="Pro zařízení čeká změna konfigurace" 
				>
					<i class="text-warning fas fa-share-square"></i>
				</a>
			</div>
			<div class="col-4 col-md-2">{{ format_date(item.first_login) }}</div>
			<div class="col-4 col-md-2">{{ format_date(item.last_login) }}</div>
			<div class="col-12 col-md-4"><i>{{ item.desc }}</i></div>
			<div class="col-6 col-md-2 text-white">
				<a :href="'device/show/' + item.id" class="text-white">Info</a>
					· 
				<a :href="'device/edit/' + item.id" class="text-white">Edit</a>
			</div>
		</div>
	</div>

	<!--<div class="row row-cols-1 g-4 mb-2" v-if="items != null">
		<div v-for="item in items" :key="item.id" class="col">
			<div class="card text-bg-dark border-warning">
				<div class="card-header">
					<h5><small>({{ item.id }})</small>{{ item.name }}</h5><div>{{ item.first_login }} | {{ item.last_login }}</div>
				</div>
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
						<a href="#" class="btn btn-outline-info">
							Viac info <i class="fa-solid fa-angles-right"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>-->
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