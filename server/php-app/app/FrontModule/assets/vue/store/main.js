import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useMainStore = defineStore('main', () => {
  /*const count = ref(0)
  const doubleCount = computed(() => count.value * 2)
  function increment() {
    count.value++
  }*/

	const basePath = ref("http://localhost/~petak23/RatatoskrIoT/server/php-app/")

	const apiPath = computed(() => basePath.value + "api/") // Cesta k API

	const appName = ref("")  // Meno aplikácie

	const links = ref([]) // Pole odkazov

	const dataRetentionDays = ref(0)
			
	const minYear = ref(2000)

  return { basePath, apiPath, appName, links, dataRetentionDays, minYear }
})
