import { createApp, defineAsyncComponent } from 'vue'
import '@fontsource/manrope/400.css'
import '@fontsource/manrope/500.css'
import '@fontsource/manrope/700.css'
import '@fontsource/manrope/800.css'
import './style.css'
import './assets/report-styles.css'
import App from './App.vue'
import router from './router'

const app = createApp(App)
app.use(router)
app.component(
	'VueDatePicker',
	defineAsyncComponent(async () => {
		await import('@vuepic/vue-datepicker/dist/main.css');
		const module = await import('@vuepic/vue-datepicker');
		return module.VueDatePicker;
	})
);
app.mount('#app')
