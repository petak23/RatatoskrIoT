<script setup>
import { onMounted } from 'vue'
import { useMainStore } from '../store/main'
import MainService from '../services/MainService';

const store = useMainStore()

const getMySettings = () => {
	MainService.getMySettings()
		.then(response => {
			//console.log(response.data)
			store.appName = response.data.appName
			store.links = response.data.links
			store.dataRetentionDays = response.data.dataRetentionDays
			store.minYear = response.data.minYear
		})
		.catch((error) => {
			console.log(error);
		})
}

const getActualUser = () => {
	MainService.getMyUserData()
		.then(response => {
			store.user = response.data
		})
		.catch((error) => {
			console.log(error);
		})
}

onMounted(() => {
	getMySettings();
	getActualUser();
})
</script>