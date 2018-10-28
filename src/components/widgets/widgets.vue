<template>
	<div v-if="widgets" class="matomo-widgets">
		<div class="matomo-widgets-description">
			<h4>{{ $t('matomo.title.details') }}</h4>
			<p>{{ $t('matomo.subline.details', {limit: defaults.limit}) }}</p>
		</div>
		<div class="widgets">
			<widget v-for="(widget, index) in widgets" :widget="widget" :currentPeriod="currentPeriod" :totalVisits="totalVisits" :lang="lang" :defaults="defaults" />
		</div>
	</div>
</template>

<script>
import Widget from './widget.vue'

export default {
	components: {Widget},
	data() { 
		return {
		}
	},
	props: {
		widgets: Array,
		currentPeriod: '',
		totalVisits: '',
		lang: '',
		defaults: Object,
	}
}
</script>