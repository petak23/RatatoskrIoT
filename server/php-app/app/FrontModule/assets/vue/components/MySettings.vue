<script setup>
import { onMounted } from 'vue'
import { useMainStore } from '../store/main'
import axios from 'axios'

const store = useMainStore()

const getMySettings = () => {
	store.baseUrl = document.getElementById('app').dataset.baseUrl + "/"
	//console.log(dataset)
	let odkaz = store.apiPath + 'homepage/myappsettings'

	axios.get(odkaz)
		.then(response => {
			//console.log(response.data)
			store.appName = response.data.appName
			store.links = response.data.links
			store.dataRetentionDays = response.data.dataRetentionDays
			store.minYear = response.data.minYear
		})
		.catch((error) => {
			console.log(odkaz);
			console.log(error);
		});
}

onMounted(() => {
	getMySettings();
})
</script>