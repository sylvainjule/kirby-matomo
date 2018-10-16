<template>
	<div class="matomo-block matomo-visits-summary">
		<div class="title">Visits summary</div>

		<div v-if="loading" class="loader"></div>
		<ul v-else>
			<li>
				<div class="icon"><svgicon icon="calendar" /></div>
				<div class="text">Today</div>
				<div class="number">{{ results.day }}</div>
			</li>
			<li>
				<div class="icon"><svgicon icon="calendar" /></div>
				<div class="text">Last 7 days</div>
				<div class="number">{{ results.week }}</div>
			</li>
			<li>
				<div class="icon"><svgicon icon="calendar" /></div>
				<div class="text">This month</div>
				<div class="number">{{ results.month }}</div>
			</li>
			<li>
				<div class="icon"><svgicon icon="calendar" /></div>
				<div class="text">This year</div>
				<div class="number">{{ results.year }}</div>
			</li>
		</ul>
	</div>
</template>


<script>
export default {
	data() { 
		return {
			loading: true,
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
	},
	created() {
		this.syncContent()
	},
	destroyed() {
	},
	methods: {
		syncContent() {
			this.loading = true
			this.$api
		        .get('matomo-panel/get-bulk-summary')
		        .then(response => {
		        	this.results.day = response[0]
		        	this.results.week = this.getLast7Days(response[1])
		        	this.results.month = response[2]
		        	this.results.year = response[3]

		        	this.loading = false
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