<template>
	<div class="widget">
		<h5>{{ $t('matomo.widgets.title.'+ widget) }}</h5>
		<div v-if="loading" class="loading"><div class="loader"></div></div>
		<ul v-else-if="!loading && !isEmpty">
			<li v-for="(result, index) in results">
				<div class="icon"><svg><use :href="findIcon(result)" /></svg></div>
				<div class="text" v-html="result.label"></div>
				<div class="number">{{ result.nb_visits }} <span class="percent">{{ visitsPercent(result) }}%</span></div>
			</li>
		</ul>
		<div v-else class="empty">{{ $t('matomo.empty') }}</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
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
		}
	},
	props: {
		currentPeriod: String,
		widget: String,
		totalVisits: Number,
		lang: String,
		loading: {
			type: Boolean,
			default: true
		},
		status: {
			type: String,
			default: 'loading'
		},
		results: {
			type: Array,
			default: []
		},
	},
	computed: {
		isEmpty() {
			return this.status == 'empty'
		},
		originalColor() {
			return this.widget == 'socials'
		},
	},
	created() {
	},
	destroyed() {
	},
	watch: {
    },
	methods: {
		visitsPercent(result) {
			let percent = result.nb_visits / this.totalVisits * 100
			percent = percent < 1 ? percent.toFixed(2) : percent.toFixed(1)

			return percent
		},
		findIcon(result) {
            let icon = this.iconsMap['page']

			if(result.label) {
				let fromLabel = this.iconsMap[result.label.toLowerCase()]
				if(fromLabel) icon = fromLabel
			}

			if(this.widget == 'keywords') {
				icon = this.iconsMap['search']
			}

			if(result.segment) {
				let fromSegment = this.iconsMap[result.segment]
				if(fromSegment) icon = fromSegment
			}

			return '#icon-matomo-'+ icon
		},
	},
}
</script>
