<template>
    <k-panel-inside>
        <k-view class="k-matomo-view">
            <k-header> {{ title }} </k-header>
            <k-grid variant="columns">
                <k-column width="3/4">
                    <matomo-main
                        :periods="main['periods']"
                        :lang="main['lang']"
                        :widgets="main['widgets']" />
                </k-column>
                <k-column width="1/4">
                    <matomo-sidebar
                        :link="sidebar['link']"
                        :summary="sidebar['summary']"
                        :realtime="sidebar['realtime']"
                        :url="sidebar['url']" />
                </k-column>
            </k-grid>
        </k-view>
    </k-panel-inside>
</template>

<script>
export default {
    props: {
        main: Array,
        sidebar: Array,
        title: String
    },
};
</script>
