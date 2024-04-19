<?php

namespace App\ThrustHelpers\Actions;

use BadChoice\Thrust\ResourceGate;
use BadChoice\Thrust\Actions\Export;

class ExportTickets extends Export
{

    public function display($resourceName, $parent_id = null)
    {
        if (! app(ResourceGate::class)->can($resourceName, 'index')) {
            return '';
        }

        $title = $this->getTitle();
        $link  = route('tickets.export',request()->query());

        return "<a class='button btn btn-secondary' href='{$link}'> <i class='fa fa-download'></i> {$title} </a>";
    }

    public function getTitle()
    {
        return 'Exportar';
    }
}
