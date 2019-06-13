<template>
	<div class="matomo-block matomo-visits-summary">
		<h5>{{ $t('matomo.title.summary') }}</h5>

		<div v-if="loading" class="loader"></div>
		<ul v-else-if="!loading && !isEmpty">
			<li>
				<div class="icon"><svg><use href="#icon-matomo-calendar" /></svg></div>
				<div class="text">{{ $t('matomo.chart.day') }}</div>
				<div class="number">{{ results.day }}</div>
			</li>
			<li>
				<div class="icon"><svg><use href="#icon-matomo-calendar" /></svg></div>
				<div class="text">{{ $t('matomo.chart.week') }}</div>
				<div class="number">{{ results.week }}</div>
			</li>
			<li>
				<div class="icon"><svg><use href="#icon-matomo-calendar" /></svg></div>
				<div class="text">{{ $t('matomo.chart.month') }}</div>
				<div class="number">{{ results.month }}</div>
			</li>
			<li>
				<div class="icon"><svg><use href="#icon-matomo-calendar" /></svg></div>
				<div class="text">{{ $t('matomo.chart.year') }}</div>
				<div class="number">{{ results.year }}</div>
			</li>
		</ul>
		<div v-else class="empty">{{ $t('matomo.empty') }}</div>
	</div>
</template>


<script>
export default {
	data() {
		return {
			loading: true,
			status: 'loading',
			results: {
				day: Number,
				week: Number,
				month: Number,
				year: Number
			}
		}
	},
	props: {
	},
	computed: {
		isEmpty() {
			return this.status == 'empty'
		},
	},
	created() {
		this.syncContent()
	},
	destroyed() {
	},
	methods: {
		syncContent() {
			this.loading = true
			this.status  = 'loading'
			this.$api
		        .get('matomo-panel/get-bulk-summary')
		        .then(response => {
		        	this.results.day = response[0]
		        	this.results.week = this.getLast7Days(response[1])
		        	this.results.month = response[2]
		        	this.results.year = response[3]

		        	this.loading = false
		        	this.status = response.status
		        })
		        .catch(error => {
		        	this.loading = false
		        	this.status = 'empty'
		        })
		},
		getLast7Days(obj) {
			let total = 0
			Object.keys(obj).forEach(function(key) {
			    total += obj[key];
			});
			return total
		}
	},
}
</script>
