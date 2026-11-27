<?php

namespace SylvainJule\Matomo;

use Kirby\Form\Field\DisplayField;
use Kirby\Reflection\Attributes\Prop;

class SidebarField extends DisplayField {
    #[Prop]
    protected bool $link = true;

    #[Prop]
    protected bool $realtime = true;

    #[Prop]
    protected bool $summary = true;

    public function props(): array {
        return [
            ...parent::props(),
            'url' => option('sylvainjule.matomo.url'),
        ];
    }
}
