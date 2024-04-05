<?php

namespace App\Thrust;

use BadChoice\Thrust\Fields\Color;
use BadChoice\Thrust\Fields\Text;
use BadChoice\Thrust\Resource;

class Channel extends Resource
{
    public static $model  = \App\Channel::class;
    public static $search = ['name'];

    public function fields()
    {
        return [
            Text::make('name'),
            Color::make('color'),
        ];
    }
}
