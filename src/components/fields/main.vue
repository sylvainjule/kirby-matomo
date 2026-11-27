<template>
    <k-field :input="false" :name="name" :type="type" :help="help" class="k-matomo-main-field">
        <matomo-main :chart="chart" :overview="overview" :periods="periods"
            :widgets="widgets" :defaults="defaults" :current-period="defaults?.period ?? ''" :lang="lang" />
    </k-field>
</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        name: String, 
        type: String, 
        help: [String, Object],
        chart: Boolean, 
        overview: Boolean, 
        periods: Array,
        widgets: [Array, Boolean], 
        defaults: Object, 
        lang: String
    }
}
</script>