<template>
	<div class="widget">
		<h5>{{widgetTitle}}</h5>
		<div v-if="loading" class="loading"><div class="loader"></div></div>
		<ul v-else-if="!loading && !isEmpty">
			<li v-for="(result, index) in results">
				<div class="icon"><svgicon :icon="findIcon(result)" :original="originalColor" /></div>
				<div class="text" v-html="result.label"></div>
				<div class="number">{{ result.nb_visits }} <span class="percent">{{ visitsPercent(result) }}%</span></div>
			</li>
		</ul>
		<div v-else class="empty">There is no data to diplay</div>
	</div>
</template>

<script>
export default {
	data() { 
		return {
			loading: true,
			status: 'loading',
			titlesMap: {
				referrerType: 'Referrers type',
				websites: 'Referrers websites',
				socials: 'Referrers socials',
				devicesType: 'Devices type',
				keywords: 'Referrers keywords',
				popularPages: 'Popular pages',
			},
			methodsMap: {
				referrerType: 'Referrers.getReferrerType',
				websites: 'Referrers.getWebsites',
				socials: 'Referrers.getSocials',
				devicesType: 'DevicesDetection.getType',
				keywords: 'Referrers.getKeywords',
				popularPages: 'Actions.getPageUrls',
			},
			iconsMap: {
				'referrerType==direct': 'arrow-right',
				'referrerType==website': 'page',
				'referrerType==search': 'search',
				'referrerType==campaign': 'globe',
				'referrerType==social': 'users',
				'dribbble': 'social-dribbble',
				'facebook': 'social-facebook',
				'flickr': 'social-flickr',
				'foursquare': 'social-foursquare',
				'github': 'social-github',
				'google%2B': 'social-google',
				'google+': 'social-google',
				'instagram': 'social-instagram',
				'linkedin': 'social-linkedin',
				'pinterest': 'social-pinterest',
				'reddit': 'social-reddit',
				'tumblr': 'social-tumblr',
				'twitter': 'social-twitter',
				'vimeo': 'social-vimeo',
				'youtube': 'social-youtube',
				'desktop': 'device-desktop',
				'smartphone': 'device-mobile',
				'feature phone': 'device-mobile',
				'tablet': 'device-tablet',
				'phablet': 'device-tablet',
				'console': 'device-console',
				'car': 'device-car',
				'car browser': 'device-car',
				'camera': 'device-camera',
				'tv': 'device-tv',
				'unknown': 'question-mark',
				'keyword not defined': 'question-mark',
				'page': 'page',
				'search': 'search',
			},
			date: 'today',
	        results: Array,
		}
	},
	props: {
		currentPeriod: String,
		widget: String,
		defaults: Object,
		totalVisits: Number,
	},
	computed: {
		isEmpty() {
			return this.status == 'empty'
		},
		originalColor() {
			return this.widget == 'socials'
		},
		widgetTitle() {
			return this.titlesMap[this.widget]
		},
	},
	created() {
	},
	destroyed() {
	},
	watch: { 
      	currentPeriod: {
      		immediate: true,
      		handler() {
	            this.syncContent()
	        }
	    }
    },
	methods: {
		visitsPercent(result) {
			let percent = result.nb_visits / this.totalVisits * 100
			percent = percent < 1 ? percent.toFixed(2) : percent.toFixed(1)

			return percent
		},
		findIcon(result) {
			if(result.label) {
				let fromLabel = this.iconsMap[result.label.toLowerCase()]
				if(fromLabel) return fromLabel
			}

			if(this.widget == 'websites' || this.widget == 'popularPages') {
				return this.iconsMap['page']
			}

			if(this.widget == 'keywords') {
				return this.iconsMap['search']
			}

			if(result.segment) {
				let fromSegment = this.iconsMap[result.segment]
				if(fromSegment) return fromSegment
			}

			return this.iconsMap['page']
		},
		syncContent() {
			this.loading = true
			this.status  = 'loading'
			this.$api
		        .get('matomo-panel/get-widget-content', {
		        	widget: this.widget,
		        	method: this.methodsMap[this.widget],
		        	period: this.currentPeriod,
		        	date: this.date,
		        	limit: this.defaults.limit,
		        })
		        .then(response => {
		        	this.results = response
		        	this.status  = response.status
		        	this.loading = false
		        })
		        .catch(error => {
		        	this.status = 'empty'
		        	this.loading = false
		        })
		},
	},
}
</script>