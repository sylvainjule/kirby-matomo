<template>
	<k-field :input="false" :name="name" :type="type" :help="help">
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
	</k-field>
</template>

<script>
export default {
	inheritAttrs: false,
    props: {
        name: String, type: String, help: [String, Object],
        period: { type: String, default: 'month' },
        lang: { type: Object, default: () => ({}) },
        uri: { type: [String, Object], default: null }
    },
	data() {
		return {
			loading: true,
			status: 'loading',
			current: {},
            all: {},
            requestId: 0,
		}
	},
	computed: {
        currentPeriod() { return this.period },
        query() {
            return {
                period: this.period,
                multilang: String(!!this.lang.multilang),
                overview: String(!!this.lang.overview),
                default: this.lang.default ?? false,
                current: this.currentLanguage,
                uri: this.currentUri
            }
        },
		isEmpty() {
			return (this.status == 'empty' || !Object.keys(this.current).length) && !this.loading
		},
		showOverview() {
			return this.lang.overview && Object.keys(this.all).length > 0
		},
		showCode() {
			return this.lang.multilang
		},
		inlineCode() {
			return !this.lang.overview
		},
		currentLanguage() {
			return this.$panel.language.code ?? this.lang.current ?? false
		},
		currentUri() {
			return this.uri && typeof this.uri === "object" ? this.uri[this.currentLanguage] : this.uri
		}
	},
    watch: {
        query: {
            immediate: true,
            handler() { this.syncContent() }
        }
    },
    beforeUnmount() { 
        this.requestId++ 
    },
    methods: {
        async syncContent() {
            const id = ++this.requestId
            this.loading = true
            this.status = 'loading'
            try {
                if(this.currentUri == null) throw new Error('Missing page URI')
                	
                const response = await this.$api.get('matomo-panel/get-page-metrics', this.query)
                if(id !== this.requestId) return
                if(response.status === 'error' || response.status === 'empty') throw new Error('No metrics')

                this.current = response.current ?? {}
                this.all = response.all ?? {}
                this.status = response.status
            } 
            catch (error) {
                if(id !== this.requestId) return
                this.current = {}
                this.all = {}
                this.status = 'empty'
            } 
            finally {
                if(id === this.requestId) this.loading = false
            }
        },
		formatTime(time) {
            let t = Math.floor(time)
            return (t - (t%=60)) / 60 + (9 < t ? '\'' : '\'0') + t
		},
	},
}
</script>
