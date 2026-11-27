<template>
    <div class="matomo-sidebar">
        <a v-if="showLink" :href="dashboardUrl" class="matomo-dashboard-link" target="_blank" rel="noopener">
            <span class="icon">
                <svg><use href="#icon-matomo-external-link" /></svg>
            </span>
            <span class="text">
                <em>{{ $t('matomo.title.external_link') }}</em>
            </span>
        </a>

        <realtime-widget v-if="showRealtime" />
        <summary-widget v-if="showSummary" />
    </div>
</template>

<script>
import RealtimeWidget from './widgets/realtime.vue'
import SummaryWidget from './widgets/summary.vue'

export default {
    components: {
        RealtimeWidget,
        SummaryWidget
    },
    props: {
        link: {
            type: Boolean,
            default: true
        },
        realtime: {
            type: Boolean,
            default: true
        },
        summary: {
            type: Boolean,
            default: true
        },
        url: {
            type: [String, Boolean],
            default: null
        }
    },
    computed: {
        dashboardUrl() {
            return this.url
        },
        showLink() {
            return !!(this.link && this.dashboardUrl)
        },
        showRealtime() {
            return this.realtime
        },
        showSummary() {
            return this.summary
        }
    },
}
</script>