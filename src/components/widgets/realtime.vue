<template>
	<div class="matomo-block matomo-realtime">
		<div v-if="!loading && !isEmpty" class="refresh" @click="syncContent">{{ $t('matomo.refresh') }}</div>

		<h5>{{ $t('matomo.title.realtime') }}</h5>
		<div v-if="!isEmpty">
			<span v-if="loading" class="loader"></span>
			<div v-else class="big-number">{{ visitors }}</div>
			<div v-if="!loading" class="details">
				<span v-if="visitors">{{ $t('matomo.visitors.caption', {}, visitors) }}.</span>
				<span v-else>{{ $t('matomo.novisitors') }}.</span></div>
		</div>
		<div v-else class="empty">{{ $t('matomo.empty') }}</div>
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
		        	this.visitors = parseFloat(response[0].visitors)
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
