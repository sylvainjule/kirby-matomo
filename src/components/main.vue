<template>
	<div class="matomo-main">
		<div class="matomo-period-selector">
			<div
                v-for="(period) in periods"
                :class="['matomo-period-option', period, {active: period == currentPeriod}]"
                @click="setCurrentPeriod(period)"
                v-bind:key="period">
                {{ $t('matomo.chart.'+ period) }}
            </div>
		</div>

		<div v-if="chart" :class="['matomo-chart', {'is-empty': chartEmpty}]">
			<div v-if="chartLoading" class="overlay"><div class="loader"></div></div>
			<div v-if="chartEmpty" class="empty"><span>{{ $t('matomo.empty') }}</span></div>
			<chart class="chart" :currentPeriod="currentPeriod" :lang="lang" @chartIsLoading="chartIsLoading" @chartIsLoaded="chartIsLoaded" @chartIsEmpty="chartIsEmpty" />
		</div>

		<overview v-if="overview" :currentPeriod="currentPeriod" :defaults="defaults" @updateVisits="updateVisits" />

		<widgets v-if="widgets" :widgets="widgets" :currentPeriod="currentPeriod" :defaults="defaults" :totalVisits="totalVisits" :lang="lang" />
	</div>
</template>

<script>
import Chart from './widgets/chart.vue'
import Overview from './widgets/overview.vue'
import Widgets from './widgets/widgets.vue'

export default {
	components: {Chart, Overview, Widgets},
    props: {
        periods: {
            type: Array,
            default: []
        },
        chart: {
            type: Boolean,
            default: true
        },
		overview: {
            type: Boolean,
            default: true
        },
        currentPeriod: {
            type: String,
            default: 'week'
        },
        lang: {
            type: String,
            default: ''
        },
        widgets: {
            type: Array,
            default: []
        }
    },
	data() {
		return {
            periods: [],
			defaults: {
				period: 'month',
				limit: 5,
			},
			totalVisits: '',
            chart: true,
			chartLoading: true,
			chartEmpty: false,
            widgets: []
		}
	},
	created() {
		this.load()
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
