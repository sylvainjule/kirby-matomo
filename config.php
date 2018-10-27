<?php 

require_once __DIR__ . '/lib/matomo.php';

Kirby::plugin('sylvainjule/matomo', array(
	'options' => array(
		'token'      => false,
		'url'        => false,
		'id'         => false,
		'active'     => true,
		'debug'      => false,
        'blacklist'  => ['127.0.0.1', '::1'],
        'trackUsers' => false,
	),
	'snippets' => array(
        'matomo' => __DIR__ . '/lib/snippets/matomo.php'
    ),
	'sections' => array(
        'matomo-main' => array(
        	'props' => array(
        		'chart' => function($chart = true) {
        			return $chart;
        		},
        		'overview' => function($overview = true) {
        			return $overview;
        		},
        		'periods' => function($periods = ['year', 'month', 'week', 'day']) {
        			return $periods;
        		},
        		'widgets' => function($widgets = ['referrerType', 'websites', 'socials', 'devicesType', 'keywords', 'popularPages']) {
        			return $widgets;
        		},
        		'defaults' => function($defaults = ['period' => 'month', 'limit' => 5]) {
        			return $defaults;
        		},
        	),
        ),
        'matomo-sidebar' => array(
        	'props' => array(
        		'link' => function($link = true) {
        			return $link;
        		},
        		'realtime' => function($realtime = true) {
        			return $realtime;
        		},
        		'summary' => function($summary = true) {
        			return $summary;
        		},
        	),
        	'computed' => array(
        		'url' => function() {
        			return option('sylvainjule.matomo.url');
        		}
        	)
        ),
        'matomo-page' => array(
        	'props' => array(
        		'overview' => function($overview = false) {
        			return $overview;
        		},
        		'period' => function($period = 'month') {
        			return $period;
        		},
        	),
        	'computed' => array(
        		'uri' => function() {
        			if(kirby()->multilang()) {
        				$uris = [];
        				foreach(kirby()->languages() as $language) {
        					$code = $language->code();
        					$uris[$code] = $this->model()->uri($code);
        				}
        				return $uris;
        			}
        			else {
        				return $this->model()->uri();
        			}
        		},
        		'lang' => function() {
        			return array(
        				'multilang' => kirby()->multilang(),
        				'default'   => kirby()->defaultLanguage() ? kirby()->defaultLanguage()->code() : false,
        				'current'   => kirby()->languageCode() ?? false,
        				'overview'  => $this->overview
        			);
        		}
        	)
        )
    ),
    'api' => array(
    	'routes' => require_once __DIR__ . '/lib/routes.php',
    ),
));