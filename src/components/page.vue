<template>
	<div class="matomo-page">
		<h5>{{ $t('matomo.widgets.page.metrics') }} {{ $t('matomo.widgets.page.'+ currentPeriod) }} <span v-if="showCode && inlineCode" class="code">{{ currentLanguage }}</span></h5>
		<div v-if="!isEmpty">
			<span v-if="loading" class="loader"></span>
			<table v-else>
			   	<tr v-if="showCode && !inlineCode">
			        <th></th>
			        <th><span class="code">{{ currentLanguage }}</span></th>
			        <th v-if="showOverview"><span>{{ $t('matomo.all') }}</span></th>
			   	</tr>
			    <tr>
			    	<td><span class="icon"><svg><use href="#icon-matomo-users" /></svg></span> {{ $t('matomo.visits') }}</td>
			    	<td>{{current.visits}}</td>
			    	<td v-if="showOverview">{{all.visits}}</td>
			   </tr>
			    <tr>
			    	<td><span class="icon"><svg><use href="#icon-matomo-duration" /></svg></span> {{ $t('matomo.duration') }}</td>
			    	<td>{{formatTime(current.duration)}}</td>
			    	<td v-if="showOverview">{{formatTime(all.duration)}}</td>
			   </tr>
			    <tr>
			    	<td><span class="icon"><svg><use href="#icon-matomo-bounce" /></svg></span> {{ $t('matomo.bounce_rate') }}</td>
			    	<td>{{current.bounce.toFixed()}}%</td>
			    	<td v-if="showOverview">{{all.bounce.toFixed()}}%</td>
			   </tr>
			    <tr>
			    	<td><span class="icon"><svg><use href="#icon-matomo-exit" /></svg></span> {{ $t('matomo.exit_rate') }}</td>
			    	<td>{{current.exit.toFixed()}}%</td>
			    	<td v-if="showOverview">{{all.exit.toFixed()}}%</td>
			   </tr>
			</table>
		</div>
		<div v-else class="empty">{{ $t('matomo.empty') }}</div>
	</div>
</template>

<script>
export default {
	components: {},
	data() {
		return {
			loading: true,
			status: 'loading',
			currentPeriod: String,
			lang: Object,
			uri: String,
			current: [],
			all: [],
		}
	},
	computed: {
		isEmpty() {
			return (this.status == 'empty' || !Object.keys(this.current).length) && !this.loading
		},
		showOverview() {
			return this.lang.overview && Object.keys(this.all).length
		},
		showCode() {
			return this.lang.multilang
		},
		inlineCode() {
			return !this.lang.overview
		},
		currentLanguage() {
			let current = this.$store.state.languages.current
			return current ? current.code : false
		},
		currentUri() {
			return this.uri.constructor == Object ? this.uri[this.currentLanguage] : this.uri
		}
	},
	created() {
		this.init()
	},
	destroyed() {
	},
	watch: {
	    currentLanguage() {
	      	this.syncContent()
	    }
	},
	methods: {
		init() {
			this.loading = true
			this.status  = 'loading'
			this.load()
		        .then(response => {
		        	this.lang = response.lang
		        	this.uri  = response.uri
		        	this.currentPeriod = response.period
		        	this.syncContent()
		        })
		},
		syncContent() {
			this.loading = true
			this.status  = 'loading'
			this.$api
		        .get('matomo-panel/get-page-metrics', {
		        	period: this.currentPeriod,
		        	multilang: this.lang.multilang,
		        	overview: this.lang.overview,
		        	default: this.lang.default,
		        	current: this.currentLanguage,
		        	uri: this.currentUri,
		        })
		        .then(response => {
		        	this.current = response.current
		        	this.all     = response.all

		        	this.loading = false
		        	this.status = response.status
		        })
		        .catch(error => {
		        	this.loading = false
		        	this.status = 'empty'
		        })
		},
		formatTime(time) {
            let t = Math.floor(time)
            return (t - (t%=60)) / 60 + (9 < t ? '\'' : '\'0') + t
		},
	},
}
</script>

<style lang="scss">
	@import '../assets/css/styles.scss'
</style>
