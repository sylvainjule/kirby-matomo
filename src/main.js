import SvgIcon from 'vue-svgicon'

import Main from './components/main.vue'
import Sidebar from './components/sidebar.vue'
import Page from './components/page.vue'

panel.plugin('sylvainjule/matomo', {
	use: [SvgIcon],
    sections: {
        'matomo-main': Main,
        'matomo-sidebar': Sidebar,
        'matomo-page': Page
    }
});