<template>
	<div class="matomo-block matomo-realtime">
		<div v-if="!loading && !isEmpty" class="refresh" @click="syncContent">{{ $t('matomo.refresh') }}</div>

		<h5>{{ $t('matomo.title.realtime') }}</h5>
		<div v-if="!isEmpty">
			<span v-if="loading" class="loader"></span>
			<div v-else class="big-number">{{ visitors }}</div>
			<div v-if="!loading" class="details">
				<span v-if="visitors">{{ visitorsString }}.</span>
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
            requestId: 0,
			status: 'loading',
			visitors: Number,
		}
	},
	computed: {
		isEmpty() {
			return this.status == 'empty'
		},
        visitorsString() {
            let t = this.$t('matomo.visitors.caption')
            return this.visitors == 1 ? t[0] : t[1]
        }
	},
	created() {
		this.syncContent()
	},
    beforeUnmount() { 
    	this.requestId++ 
    },
	methods: {
		syncContent() {
            const id = ++this.requestId
			this.loading = true
			this.status  = 'loading'

			this.$api
		        .get('matomo-panel/get-realtime-number')
		        .then(response => {
                    if(id !== this.requestId) return
                    if(!response || response.status === 'error' || response.status === 'empty') throw new Error('No metrics')

		        	this.visitors = parseFloat(response[0].visitors)
		        	this.loading  = false
					this.status   = response.status
		        })
		        .catch(error => {
                    if(id !== this.requestId) return
                    	
		        	this.loading = false
		        	this.status  = 'empty'
		        })
		},
	},
}
</script>