<?php

namespace App\Thrust;

use BadChoice\Thrust\Fields\Color;
use BadChoice\Thrust\Fields\Text;
use BadChoice\Thrust\Resource;

class Category extends Resource
{
    public static $model  = \App\Category::class;
    public static $search = ['name'];

    public function fields()
    {
        return [
            Text::make('name'),
            Color::make('color'),
        ];
    }
}
