<template>
	<div class="matomo-sidebar">
		<a v-if="showLink" :href="url" class="matomo-dashboard-link" target="_blank" rel="noopener">
			<span class="icon"><svgicon icon="external-link" /></span>
			<span class="text"><em>Matomo dashboard</em></span>
		</a>

		<realtime-widget v-if="realtime" />

		<summary-widget v-if="summary" />
	</div>
</template>

<script>
import RealtimeWidget from './widgets/realtime.vue'
import SummaryWidget from './widgets/summary.vue'

import '../assets/svg/compiled'

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
	props: {
        parent: String,
        name: String,
	},
	computed: {
		showLink() {
			return this.link && this.url
		}
	},
	created() {
		this.$api
	        .get(this.parent + "/sections/" + this.name)
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