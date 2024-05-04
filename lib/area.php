<?php

return [
    'matomo' => function ($kirby) {
        $label = $kirby->option('sylvainjule.matomo.area.label');
        return [
            'label' => $label,
            'icon'  => 'line-chart',
            'link'  => 'matomo',
            'menu'  => $kirby->option('sylvainjule.matomo.area'),
            'views' => [
                [
                    'pattern' => 'matomo',
                    'action'  => function() use($label) {
                        $lang    = kirby()->user()->language();
                        $periods = ['year', 'month', 'week', 'day'];
                        $url     = option('sylvainjule.matomo.url');
                        $widgets = ['referrerType', 'websites', 'socials', 'devicesType', 'keywords', 'popularPages'];

                        return [
                            'component'  => 'matomo',
                            'title'      => $label,
                            'props' => [
                                'title' => kirby()->option('sylvainjule.matomo.area.label'),
                                'main'  => [
                                    'periods' => $periods,
                                    'lang'    => $lang,
                                    'widgets' => $widgets
                                ],
                                'sidebar' => [
                                    'link'     => true,
                                    'realtime' => true,
                                    'summary'  => true,
                                    'url'      => $url,
                                ]
                            ]
                        ];
                    }
                ]
            ]
        ];
    }
];
