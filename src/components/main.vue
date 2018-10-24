<template>
	<div class="matomo-main">
		<div class="matomo-period-selector">
			<div v-for="(period, index) in periods" :class="['matomo-period-option', period, {active: period == currentPeriod}]" @click="setCurrentPeriod(period)">{{periodString(period)}}</div>
		</div>
		
		<div v-if="chart" class="matomo-chart">
			<div v-if="chartLoading" class="overlay"><div class="loader"></div></div>
			<chart class="chart" :currentPeriod="currentPeriod" @chartIsLoading="chartIsLoading" @chartIsLoaded="chartIsLoaded" />
		</div>

		<overview v-if="overview" :currentPeriod="currentPeriod" :defaults="defaults" @updateVisits="updateVisits" />

		<div v-if="widgets" class="matomo-widgets">
			<div class="matomo-widgets-description">
				<h4>Details</h4>
				<p>Top 5 – Ranked by visits count</p>
			</div>
			<div class="widgets">
				<widget v-for="(widget, index) in widgets" :widget="widget" :currentPeriod="currentPeriod" :totalVisits="totalVisits" :defaults="defaults" />
			</div>
		</div>
	</div>
</template>

<script>
import Chart from './widgets/chart.vue'
import Overview from './widgets/overview.vue'
import Widget from './widgets/widget.vue'

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
			chart: false,
			overview: false,
			widgets: Array,
			chartLoading: true,
        	rangeStrings: {
				year: 'This year',
				month: 'This month',
				week: 'Last 7 days',
				day: 'Today',
			},
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
		},
		periodString(period) {
			return this.rangeStrings[period]
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