<template>
    <k-field :input="false" :name="name" :type="type" :help="help" class="k-matomo-sidebar-field">
        <matomo-sidebar :link="link" :realtime="realtime" :summary="summary" :url="url" />
    </k-field>
</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        name: String, 
        type: String, 
        help: [String, Object],
        link: Boolean, 
        realtime: Boolean, 
        summary: Boolean, 
        url: [String, Boolean]
    }
}
</script>
