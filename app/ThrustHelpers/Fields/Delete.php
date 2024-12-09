<?php

namespace App\ThrustHelpers\Fields;

use BadChoice\Thrust\Facades\Thrust;
use BadChoice\Thrust\Fields\Field;

class Delete extends Field
{
    public $showInEdit          = false;
    public $withoutIndexHeader  = true;
    public $rowClass            = 'action';
    public $policyAction        = 'delete';

    public function displayInIndex($object)
    {
        $link = route('user.delete', [$object->id]);

        return "<a class='delete-resource thrust-delete' data-delete='confirm resource' href='{$link}'></a>";
    }

    public function displayInEdit($object, $inline = false)
    {
    }
}
