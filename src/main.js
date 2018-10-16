import SvgIcon from 'vue-svgicon'

import Main from './components/main.vue'
import Sidebar from './components/sidebar.vue'

panel.plugin('sylvainjule/matomo', {
	use: [SvgIcon],
    sections: {
        'matomo-main': Main,
        'matomo-sidebar': Sidebar
    }
});