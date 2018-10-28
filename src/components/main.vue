<template>
	<div class="matomo-main">
		<div class="matomo-period-selector">
			<div v-for="(period, index) in periods" :class="['matomo-period-option', period, {active: period == currentPeriod}]" @click="setCurrentPeriod(period)">{{ $t('matomo.chart.'+ period) }}</div>
		</div>
		
		<div v-if="chart" :class="['matomo-chart', {'is-empty': chartEmpty}]">
			<div v-if="chartLoading" class="overlay"><div class="loader"></div></div>
			<div v-if="chartEmpty" class="empty"><span>{{ $t('matomo.empty') }}</span></div>
			<chart class="chart" :currentPeriod="currentPeriod" :lang="lang" @chartIsLoading="chartIsLoading" @chartIsLoaded="chartIsLoaded" @chartIsEmpty="chartIsEmpty" />
		</div>

		<overview v-if="overview" :currentPeriod="currentPeriod" :defaults="defaults" @updateVisits="updateVisits" />

		<div v-if="widgets" class="matomo-widgets">
			<div class="matomo-widgets-description">
				<h4>{{ $t('matomo.title.details') }}</h4>
				<p>{{ $t('matomo.subline.details', {limit: defaults.limit}) }}</p>
			</div>
			<div class="widgets">
				<widget v-for="(widget, index) in widgets" :widget="widget" :currentPeriod="currentPeriod" :totalVisits="totalVisits" :lang="lang" :defaults="defaults" />
			</div>
		</div>
	</div>
</template>

<script>
import Chart from './widgets/chart.vue'
import Overview from './widgets/overview.vue'
import Widget from './widgets/widget.vue'

import '../assets/svg/compiled'

export default {
	components: {Chart, Overview, Widget},
	data() { 
		return {
			currentPeriod: '',
        	periods: Array,
			defaults: {
				period: 'month',
				limit: 5,
			},
			totalVisits: '',
			lang: '',
			chart: false,
			overview: false,
			widgets: Array,
			chartLoading: true,
			chartEmpty: false,		
		}
	},
	props: {
        parent: String,
        name: String,
	},
	computed: {
	},
	created() {
		this.$api
	        .get(this.parent + "/sections/" + this.name)
	        .then(response => {
	        	this.periods  = response.periods
	        	this.defaults = Object.assign(this.defaults, response.defaults)
	        	this.widgets  = response.widgets
	        	this.chart    = response.chart
	        	this.overview = response.overview
	        	this.lang     = response.lang

        		this.currentPeriod = response.defaults.period ? response.defaults.period : response.periods[0]
	        })
	},
	destroyed() {
	},
	methods: {
		chartIsLoading() {
			this.chartLoading = true
		},
		chartIsLoaded() {
			this.chartLoading = false
			this.chartEmpty   = false
		},
		chartIsEmpty() {
			this.chartLoading = false
			this.chartEmpty   = true
		},
		setCurrentPeriod(period) {
			this.currentPeriod = period
		},
		updateVisits(value) {
			this.totalVisits = value
		}
	},
}
</script>

<style lang="scss">
	@import '../assets/css/styles.scss'
</style>