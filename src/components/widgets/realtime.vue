<template>
	<div class="matomo-block matomo-realtime">
		<div v-if="!loading" class="refresh" @click="syncContent">Refresh</div>

		<div class="title">Realtime</div>
		<span v-if="loading" class="loader"></span>
		<div v-else class="big-number">{{ visitors }}</div>
		<div v-if="!loading" class="details">{{ visitorsString }}</div>
	</div>
</template>

<script>
export default {
	data() { 
		return {
			loading: true,
			visitors: Number,
		}
	},
	props: {
	},
	computed: {
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
			this.$api
		        .get('matomo-panel/get-realtime-number')
		        .then(response => {
		        	this.visitors = response[0].visitors
		        	this.loading = false
		        })
		},
	},
}
</script>