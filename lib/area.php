<?php

return [
    'matomo' => function ($kirby) {
        $label = $kirby->option('sylvainjule.matomo.label');
        return [
            'label' => $label,
            'icon' => 'line-chart',
            'link' => 'matomo',
            'menu' => true,
            'views' => [
                [
                    'pattern' => 'matomo',
                    'action' => function () use ($label) {
                        $lang = kirby()->user()->language();
                        $periods = ['year', 'month', 'week', 'day'];
                        $url = option('sylvainjule.matomo.url');
                        $widgets = ["referrerType", "websites", "socials", "devicesType", "keywords", "popularPages"];
                        return [
                            'component' => 'matomo',
                            'title' => $label . ': ' . t('matomo.title.overview'),
                            'breadcrumb' => [
                                [
                                    'label' => t('matomo.title.overview'),
                                    'link' => 'matomo'
                                ]
                            ],
                            'props' => [
                                'main' => [
                                    'periods' => $periods,
                                    'lang' => $lang,
                                    'widgets' => $widgets
                                ],
                                'sidebar' => [
                                    'link' => true,
                                    'realtime' => true,
                                    'summary' => true,
                                    'url' => $url,
                                ]
                            ]
                        ];
                    }
                ]
            ]
        ];
    }
];
