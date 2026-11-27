<template>
    <div class="matomo-main">
            <div class="matomo-period-selector">
                <div v-for="period in periods" :class="['matomo-period-option', period, {active: period == activePeriod}]" @click="setCurrentPeriod(period)" :key="period">
                    {{ $t('matomo.chart.' + period) }}
                </div>
            </div>

            <div v-if="chart" :class="['matomo-chart', {'is-empty': chartEmpty}]">

                <div v-if="chartLoading" class="overlay">
                    <div class="loader"></div>
                </div>
                <div v-if="chartEmpty" class="empty">
                    <span>{{ $t('matomo.empty') }}</span>
                </div>
                <chart class="chart" :currentPeriod="activePeriod" :lang="lang" @chartIsLoading="chartIsLoading" @chartIsLoaded="chartIsLoaded" @chartIsEmpty="chartIsEmpty"/>
            </div>

            <overview v-if="overview" :currentPeriod="activePeriod" :defaults="widgetDefaults" @updateVisits="updateVisits" />

            <widgets v-if="activeWidgets" :widgets="activeWidgets" :currentPeriod="activePeriod" :defaults="widgetDefaults" :totalVisits="totalVisits" :lang="lang" />

    </div>
</template>

<script>
import Chart    from './widgets/chart.vue'
import Overview from './widgets/overview.vue'
import Widgets  from './widgets/widgets.vue'

export default {
	components: {Chart, Overview, Widgets},
    props: {
	    periods: {
	        type: Array,
	        default: () => []
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
	    defaults: {
	        type: Object,
	        default: () => ({ period: 'month', limit: 5 })
	    },
	    widgets: {
	        type: [Array, Boolean],
	        default: () => []
	    }
	},
	data() {
	    return {
	        selectedPeriod: null,
	        totalVisits: 0,
	        chartLoading: true,
	        chartEmpty: false
	    }
	},
	computed: {
        activeWidgets() {
            return this.widgets === true ? ['referrerType', 'websites', 'socials', 'devicesType', 'keywords', 'popularPages'] : this.widgets
        },
        widgetDefaults() {
            return { period: 'month', limit: 5, ...this.defaults }
        },
	    initialPeriod() {
	        const periods = this.periods
	        const period = this.currentPeriod

	        if(periods.length) {
	            return periods.includes(period) ? period : periods[0]
	        }

	        return period || this.widgetDefaults.period
	    },
	    activePeriod() {
	        return this.selectedPeriod && (!this.periods.length || this.periods.includes(this.selectedPeriod)) ? this.selectedPeriod : this.initialPeriod
	    }
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
		    this.selectedPeriod = period
		},
		updateVisits(value) {
			this.totalVisits = value
		}
	},
}
</script>