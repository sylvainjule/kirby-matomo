<template>
	<div class="matomo-sidebar">
		<a v-if="showLink" :href="url" class="matomo-dashboard-link" target="_blank" rel="noopener">
			<span class="icon"><svg><use href="#icon-matomo-external-link" /></svg></span>
			<span class="text"><em>{{ $t('matomo.title.external_link') }}</em></span>
		</a>

		<realtime-widget v-if="realtime" />

		<summary-widget v-if="summary" />
	</div>
</template>

<script>
import RealtimeWidget from './widgets/realtime.vue'
import SummaryWidget from './widgets/summary.vue'

export default {
	components: {RealtimeWidget, SummaryWidget},
	data() {
		return {
			url: String,
			link: false,
			realtime: false,
			summary: false,
		}
	},
	computed: {
		showLink() {
			return this.link && this.url
		}
	},
	created() {
		this.load()
	        .then(response => {
	        	this.url = response.url
	        	this.link = response.link
	        	this.realtime = response.realtime
	        	this.summary = response.summary
	        })
	},
	destroyed() {
	},
	methods: {
	},
}
</script>

<style lang="scss">
	@import '../assets/css/styles.scss'
</style>
