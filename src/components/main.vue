<template>
    <div class="matomo-main">
        <template v-if="isReady">
            <div class="matomo-period-selector">
                <div v-for="period in settings.periods" :class="['matomo-period-option', period, {active: period == activePeriod}]" @click="setCurrentPeriod(period)" :key="period">
                    {{ $t('matomo.chart.' + period) }}
                </div>
            </div>

            <div v-if="settings.chart" :class="['matomo-chart', {'is-empty': chartEmpty}]">

                <div v-if="chartLoading" class="overlay">
                    <div class="loader"></div>
                </div>
                <div v-if="chartEmpty" class="empty">
                    <span>{{ $t('matomo.empty') }}</span>
                </div>
                <chart class="chart" :currentPeriod="activePeriod" :lang="settings.lang" @chartIsLoading="chartIsLoading" @chartIsLoaded="chartIsLoaded" @chartIsEmpty="chartIsEmpty"/>
            </div>

            <overview v-if="settings.overview" :currentPeriod="activePeriod" :defaults="defaults" @updateVisits="updateVisits" />

            <widgets v-if="settings.widgets" :widgets="settings.widgets" :currentPeriod="activePeriod" :defaults="defaults" :totalVisits="totalVisits" :lang="settings.lang" />

        </template>
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
	    widgets: {
	        type: [Array, Boolean],
	        default: () => []
	    }
	},
	data() {
	    return {
	        sectionOptions: null,
	        selectedPeriod: null,
	        totalVisits: '',
	        chartLoading: true,
	        chartEmpty: false
	    }
	},
	computed: {
	    isSection() {
	        return typeof this.load === 'function' && !!this.parent && !!this.name
	    },
	    isReady() {
	        return !this.isSection || this.sectionOptions !== null
	    },
	    settings() {
	        return this.sectionOptions ?? this.$props
	    },
	    defaults() {
	        return {
	            period: 'month',
	            limit: 5,
	            ...this.settings.defaults
	        }
	    },
	    initialPeriod() {
	        const periods = this.settings.periods
	        const period  = this.isSection ? this.settings.defaults?.period : this.currentPeriod

	        if(periods.length) {
	            return periods.includes(period) ? period : periods[0]
	        }

	        return period || this.defaults.period
	    },
	    activePeriod() {
	        return this.selectedPeriod ?? this.initialPeriod
	    }
	},
	created() {
	    if(this.isSection) {
	        this.load().then(response => {
	            this.sectionOptions = response
	        })
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

<style lang="scss">
	@import '../assets/css/styles.scss'
</style>
