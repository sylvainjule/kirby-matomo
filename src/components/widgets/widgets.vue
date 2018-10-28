<template>
	<div v-if="widgets" class="matomo-widgets">
		<div class="matomo-widgets-description">
			<h4>{{ $t('matomo.title.details') }}</h4>
			<p>{{ $t('matomo.subline.details', {limit: defaults.limit}) }}</p>
		</div>
		<div class="widgets">
			<widget v-for="(widget, index) in widgets" :widget="widget" :totalVisits="totalVisits" :lang="lang" :loading="loading" :status="status" :results="results[index]" />
		</div>
	</div>
</template>

<script>
import Widget from './widget.vue'

export default {
	components: {Widget},
	data() { 
		return {
			loading: true,
			status: 'loading',
			date: 'today',
			results: [],
		}
	},
	props: {
		widgets: Array,
		currentPeriod: '',
		totalVisits: Number,
		lang: '',
		defaults: Object,
	},
	watch: { 
      	currentPeriod: {
      		immediate: true,
      		handler(newVal, oldVal) {
	            if(newVal != '') this.syncContent()
	        }
	    },
    },
    methods: {
		syncContent() {
			this.loading = true
			this.status  = 'loading'
			this.$api
		        .get('matomo-panel/get-bulk-widgets', {
		        	widgets: JSON.stringify(this.widgets),
		        	period: this.currentPeriod,
		        	date: this.date,
		        	lang: this.lang,
		        	limit: this.defaults.limit,
		        })
		        .then(response => {
		        	this.results = response
		        	this.status  = response.status
		        	this.loading = false
		        })
		        .catch(error => {
		        	this.status = 'empty'
		        	this.loading = false
		        })
		},
    }

}
</script>