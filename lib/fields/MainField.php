<?php

namespace SylvainJule\Matomo;

use Kirby\Form\Field\DisplayField;
use Kirby\Reflection\Attributes\Prop;

class MainField extends DisplayField {
    #[Prop]
    protected bool $chart = true;

    #[Prop]
    protected bool $overview = true;

    #[Prop]
    protected array $periods = ['year', 'month', 'week', 'day'];

    #[Prop]
    protected array|bool $widgets = ['referrerType', 'websites', 'socials', 'devicesType', 'keywords', 'popularPages'];

    #[Prop]
    protected array $defaults = ['period' => 'month', 'limit' => 5];

    public function props(): array {
        return [
            ...parent::props(),
            'lang' => $this->kirby()->user()?->language() ?? 'en',
        ];
    }
}
