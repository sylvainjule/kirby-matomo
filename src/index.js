import Main from './components/main.vue'
import Sidebar from './components/sidebar.vue'
import Page from './components/page.vue'

import './assets/svg/icons.js'

panel.plugin('sylvainjule/matomo', {
    sections: {
        'matomo-main': Main,
        'matomo-sidebar': Sidebar,
        'matomo-page': Page
    }
});