<template>
	<div class="matomo-block matomo-realtime">
		<div v-if="!loading && !isEmpty" class="refresh" @click="syncContent">Refresh</div>

		<h5>Realtime</h5>
		<div v-if="!isEmpty">
			<span v-if="loading" class="loader"></span>
			<div v-else class="big-number">{{ visitors }}</div>
			<div v-if="!loading" class="details">{{ visitorsString }}</div>
		</div>
		<div v-else class="empty">There is no data to diplay</div>
	</div>
</template>

<script>
export default {
	data() { 
		return {
			loading: true,
			status: 'loading',
			visitors: Number,
		}
	},
	props: {
	},
	computed: {
		isEmpty() {
			return this.status == 'empty'
		},
		visitorsString() {
			if(this.visitors == 0) {
				return "There are currently no visitors browsing the website."
			}
			else if(this.visitors == 1) {
				return "visitor is currently browsing the website."
			}
			else if(this.visitors > 1) {
				return "visitors are currently browsing the website."
			}
		}
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
		        .get('matomo-panel/get-realtime-number')
		        .then(response => {
		        	this.visitors = response[0].visitors
		        	this.loading  = false
					this.status   = response.status
		        })
		        .catch(error => {
		        	this.loading = false
		        	this.status  = 'empty'
		        })
		},
	},
}
</script>