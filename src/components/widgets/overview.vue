<template>
	<div class="matomo-overview">
		<div class="matomo-overview-description">
			<h4>{{ $t('matomo.title.overview') }}</h4>
			<p>{{ $t('matomo.subline.overview') }} {{ $t('matomo.subline.overview.'+ currentPeriod) }}</p>
		</div>
		<div class="matomo-overview-ctn">
			<div v-if="!isEmpty" class="matomo-overview-block">
				<div class="top">
					<h5>{{ $t('matomo.visits') }}</h5>
					<div class="big-number">
						<span v-if="loading"><span class="loader"></span></span>
						<span v-else>{{current.visits}}</span>
					</div>
					<div class="details">{{ $t('matomo.visits').toLowerCase() }} {{ $t('matomo.period.'+currentPeriod) }}.</div>
				</div>
				<div v-if="isFinite(visitsDiff.diff)" :class="['difference', visitsDiff.status, {grey: loading}]">
					<span v-if="loading"><span class="loader"></span></span>
					<span v-else>{{visitsDiff.string}}%</span>
					<div v-if="!loading" class="icon"><svg><use href="#icon-matomo-arrow-up" /></svg></div>
				</div>
			</div>
			<div v-if="!isEmpty" class="matomo-overview-block">
				<div class="top">
					<h5>{{ $t('matomo.duration') }}</h5>
					<div class="big-number">
						<span v-if="loading"><span class="loader"></span></span>
						<span v-else>{{durationString}}</span></div>
					<div class="details">{{ $t('matomo.duration.caption') }}.</div>
				</div>
				<div v-if="isFinite(durationDiff.diff)" :class="['difference', durationDiff.status, {grey: loading}]">
					<span v-if="loading"><span class="loader"></span></span>
					<span v-else>{{durationDiff.string}}%</span>
					<div v-if="!loading" class="icon"><svg><use href="#icon-matomo-arrow-up" /></svg></div>
				</div>
			</div>
			<div v-if="!isEmpty" class="matomo-overview-block">
				<div class="top">
					<h5>{{ $t('matomo.bounce') }}</h5>
					<div class="big-number">
						<span v-if="loading"><span class="loader"></span></span>
						<span v-else>{{bounceString}}</span>
					</div>
					<div class="details">{{ $t('matomo.bounce.caption') }}.</div>
				</div>
				<div v-if="isFinite(bounceDiff.diff)" :class="['difference', bounceDiff.status, {grey: loading}]">
					<span v-if="loading"><span class="loader"></span></span>
					<span v-else>{{bounceDiff.string}}%</span>
					<div v-if="!loading" class="icon"><svg><use href="#icon-matomo-arrow-up" /></svg></div>
				</div>
			</div>
			<div v-if="!isEmpty" class="matomo-overview-block">
				<div class="top">
					<h5>{{ $t('matomo.actions') }}</h5>
					<div class="big-number">
						<span v-if="loading"><span class="loader"></span></span>
						<span v-else>{{actionsString}}</span>
					</div>
					<div class="details">{{ $t('matomo.actions.caption') }}.</div>
				</div>
				<div v-if="isFinite(actionsDiff.diff)" :class="['difference', actionsDiff.status, {grey: loading}]">
					<span v-if="loading"><span class="loader"></span></span>
					<span v-else>{{actionsDiff.string}}%</span>
					<div v-if="!loading" class="icon"><svg><use href="#icon-matomo-arrow-up" /></svg></div>
				</div>
			</div>
			<div v-else class="empty">{{ $t('matomo.empty') }}</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			loading: true,
			status: 'loading',
			widget: 'overview',
			method: 'VisitsSummary.get',
			date: 'today',
			limit: false,
	        current: {
	        	visits: null,
	        	duration: null,
	        	bounce: null,
	        	actions: null,
	        },
	        prev: {
	        	visits: null,
	        	duration: null,
	        	bounce: null,
	        	actions: null,
	        },
		}
	},
	props: {
		currentPeriod: String,
	},
	computed: {
		isEmpty() {
			return this.status == 'empty'
		},
		visitsDiff() {
			let diff = this.diff(this.current.visits, this.prev.visits)
			let status = diff > 0 ? 'positive' : 'negative'
			let prefix = diff > 0 ? '+ ' : '- '
			let string = prefix + Math.abs(diff)
			return {
				string: string,
				status: status,
                diff: diff
			}
		},
		durationDiff() {
			let diff = this.diff(this.current.duration, this.prev.duration)
			let status = diff > 0 ? 'positive' : 'negative'
			let prefix = diff > 0 ? '+ ' : '- '
			let string = prefix + Math.abs(diff)
			return {
				string: string,
				status: status,
                diff: diff
			}
		},
		bounceDiff() {
			let diff = this.diff(this.current.bounce, this.prev.bounce)
			let status = diff > 0 ? 'positive' : 'negative'
			let prefix = diff > 0 ? '+ ' : '- '
			let string = prefix + Math.abs(diff)
			return {
				string: string,
				status: status,
                diff: diff
			}
		},
		actionsDiff() {
			let diff = this.diff(this.current.actions, this.prev.actions)
			let status = diff > 0 ? 'positive' : 'negative'
			let prefix = diff > 0 ? '+ ' : '- '
			let string = prefix + Math.abs(diff)
			return {
				string: string,
				status: status,
                diff: diff
			}
		},
		durationString() {
			return this.formatTime(this.current.duration)
		},
        bounceString() {
            return !isFinite(this.current.bounce) ? '–' : this.current.bounce + '%'
        },
        actionsString() {
            return !isFinite(this.current.actions) ? '–' : this.current.actions + '%'
        },
		isPositive() {
			return !this.loading
		},
		isNegative() {
			return !this.loading
		},
		config() {
			let config = {}

			if(this.currentPeriod == 'year') {
				config = {
		        	method: this.method,
		        	period: 'month',
		        	date: 'last24',
		        }
			}
			if(this.currentPeriod == 'month') {
				config = {
		        	method: this.method,
		        	period: 'day',
		        	date: 'last62',
		        }
			}
			if(this.currentPeriod == 'week') {
				config = {
		        	method: this.method,
		        	period: 'day',
		        	date: 'last14',
		        }
			}
			if(this.currentPeriod == 'day') {
				config = {
		        	method: this.method,
		        	period: 'day',
		        	date: 'last2',
		        }
			}

			return config
		}
	},
	created() {
	},
	destroyed() {
	},
	watch: {
      	currentPeriod: {
      		immediate: true,
      		handler(newVal, oldVal) {
      			this.syncContent()
	        }
	    }
    },
	methods: {
		syncContent() {
			this.loading = true
			this.status  = 'loading'
		    // get metrics for the current [year, month, week, day]
			this.$api
		        .get('matomo-panel/get-overview-content', this.config)
		        .then(response => {
		        	let current = {}
		        	let prev = {}
		        	let length = response.length

		        	if(this.currentPeriod == 'year') {
		        		let currentYearKeys = this.currentYearKeys(response)
		        		let prevYearKeys = this.prevYearKeys(response, currentYearKeys.length)

		        		current = this.toObj(currentYearKeys, response)
		        		prev = this.toObj(prevYearKeys, response)
		        	}
		        	if(this.currentPeriod == 'month') {
		        		let currentMonthKeys = this.currentMonthKeys(response)
		        		let prevMonthKeys = this.prevMonthKeys(response, currentMonthKeys.length)

		        		current = this.toObj(currentMonthKeys, response)
		        		prev = this.toObj(prevMonthKeys, response)
		        	}
		        	if(this.currentPeriod == 'week') {
		        		prev = this.firstN(response, 7)
		        		current = this.lastN(response, 7)
		        	}
		        	if(this.currentPeriod == 'day') {
		        		prev = this.firstN(response, 1)
		        		current = this.lastN(response, 1)
		        	}

		        	this.current.visits   = this.sum(current, 'nb_visits')
		        	this.prev.visits      = this.sum(prev, 'nb_visits')

		        	this.current.duration   = this.average(current, 'avg_time_on_site')
		        	this.prev.duration      = this.average(prev, 'avg_time_on_site')

		        	this.current.bounce   = this.average(current, 'bounce_rate').toFixed()
		        	this.prev.bounce      = this.average(prev, 'bounce_rate').toFixed()

		        	this.current.actions   = this.average(current, 'nb_actions_per_visit').toFixed(1)
		        	this.prev.actions      = this.average(prev, 'nb_actions_per_visit').toFixed(1)

		        	this.loading = false
		        	this.status = response.status
		        	this.$emit('updateVisits', this.current.visits)
		        })
		        .catch(error => {
		        	this.loading = false
		        	this.status = 'empty'
		        })
		},
		formatTime(time) {
            let t = Math.floor(time)
            return isFinite(t) ? (t - (t%=60)) / 60 + (9 < t ? 'mn ' : 'mn 0') + t + 's' : '–'
		},
		firstN(obj, n) {
  			return Object.keys(obj).slice(0, n).reduce(function(el, index) {
      			el[index] = obj[index]
      			return el;
    		}, {})
  		},
		lastN(obj, n) {
  			return Object.keys(obj).slice(Object.keys(obj).length - n).reduce(function(el, index) {
      			el[index] = obj[index]
      			return el;
    		}, {})
  		},
  		currentMonthKeys(obj) {
  			let d = new Date()
		    let currentMonth = ('0' + (d.getMonth() + 1)).slice(-2)
		    return Object.keys(obj).filter(key => {
                return key.split('-')[1] == currentMonth;
            })
  		},
  		prevMonthKeys(obj, length) {
  			let d = new Date()
  			let prevMonth = d.getMonth()
  				prevMonth = prevMonth == 0 ? 12 : prevMonth
  				prevMonth = ('0' + prevMonth).slice(-2)

			return Object.keys(obj).filter(key => {
                return key.split('-')[1] == prevMonth;
            }).slice(0, length)
  		},
  		currentYearKeys(obj) {
  			let d = new Date()
		    let currentYear = d.getFullYear()
		    return Object.keys(obj).filter(key => {
                return key.split('-')[0] == currentYear;
            })
  		},
  		prevYearKeys(obj, length) {
  			let d = new Date()
  			let prevYear = d.getFullYear() - 1

			return Object.keys(obj).filter(key => {
                return key.split('-')[0] == prevYear;
            }).slice(0, length)
  		},
  		toObj(arr, obj) {
  			return arr.reduce(function(el, index) {
      			el[index] = obj[index]
      			return el;
    		}, {})
  		},
  		sum(obj, property) {
  			let sum = 0;

			for(var el in obj) {
			    if(obj.hasOwnProperty(el)) {
			    	if(obj[el].hasOwnProperty(property)) {
			    		sum += parseFloat(obj[el][property]);
			    	}
			    }
			}

			return sum;
  		},
  		average(obj, property) {
  			let sum = this.sum(obj, property)
  			return sum / Object.keys(obj).filter(key => {
                             return Object.keys(obj[key]).length
                         }).length
  		},
  		diff(current, prev) {
  			let diff = (current - prev) / prev * 100
            return diff.toFixed(2)
  		}
	},
}
</script>
