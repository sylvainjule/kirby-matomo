<?php

namespace SylvainJule\Matomo;

use Kirby\Form\Field\DisplayField;
use Kirby\Reflection\Attributes\Prop;

use Kirby\Cms\Page;

class PageField extends DisplayField {
    #[Prop]
    protected bool $overview = false;

    #[Prop]
    protected string $period = 'month';

    public function props(): array {
        $kirby = $this->kirby();
        $model = $this->model();
        $uri   = null;

        if($model instanceof Page) {
            if ($kirby->multilang()) {
                $uri = [];
                foreach ($kirby->languages() as $language) {
                    $code = $language->code();
                    $uri[$code] = $model->uri($code);
                }
            } else {
                $uri = $model->uri();
            }
        }

        return [
            ...parent::props(),
            'uri' => $uri,
            'lang' => [
                'multilang' => $kirby->multilang(),
                'default' => $kirby->defaultLanguage()?->code() ?? false,
                'current' => $kirby->languageCode() ?? false,
                'overview' => $this->overview,
            ],
        ];
    }
}
