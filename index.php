<?php

require_once __DIR__ . '/lib/matomo.php';

load([
    'SylvainJule\\Matomo\\MainField'    => 'lib/fields/MainField.php',
    'SylvainJule\\Matomo\\SidebarField' => 'lib/fields/SidebarField.php',
    'SylvainJule\\Matomo\\PageField'    => 'lib/fields/PageField.php',
], __DIR__);

if (property_exists(\Kirby\Blueprint\Normalizer::class, 'sectionFields')) {
    \Kirby\Blueprint\Normalizer::$sectionFields += [
        'matomo-main'    => 'matomo-main',
        'matomo-sidebar' => 'matomo-sidebar',
        'matomo-page'    => 'matomo-page',
    ];
}

Kirby::plugin('sylvainjule/matomo', array(
    'areas' => require_once __DIR__ . '/lib/area.php',
	'options' => array(
		'token'          => false,
		'url'            => false,
		'id'             => false,
		'active'         => true,
		'debug'          => false,
        'blacklist'      => ['127.0.0.1', '::1'],
        'trackUsers'     => false,
        'area'           => true,
        'area.label'     => 'Matomo',
        'area.headline'  => null,
        'basicAuth'      => null,
        'disableCookies' => false,
        'setDoNotTrack'  => false,
	),
    'blueprints' => array(
        'blocks/matomo-opt-out' => __DIR__ . '/lib/blueprints/blocks/matomo-opt-out.yml'
    ),
	'snippets' => array(
        'matomo' => __DIR__ . '/lib/snippets/matomo.php',
        'blocks/matomo-opt-out' => __DIR__ . '/lib/snippets/blocks/matomo-opt-out.php'
    ),
    'fields' => [
        'matomo-main'    => \SylvainJule\Matomo\MainField::class,
        'matomo-sidebar' => \SylvainJule\Matomo\SidebarField::class,
        'matomo-page'    => \SylvainJule\Matomo\PageField::class,
    ],
    'translations' => array(
        'en' => require_once __DIR__ . '/lib/languages/en.php',
        'de' => require_once __DIR__ . '/lib/languages/de.php',
        'fr' => require_once __DIR__ . '/lib/languages/fr.php',
        'es' => require_once __DIR__ . '/lib/languages/es.php',
    ),
    'api' => array(
    	'routes' => require_once __DIR__ . '/lib/routes.php',
    ),
));
