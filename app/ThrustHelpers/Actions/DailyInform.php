<?php

namespace App\ThrustHelpers\Actions;

use BadChoice\Thrust\Actions\MainAction;
use App\Ticket;
use App\ThrustHelpers\Filters\TicketDailyInformFilter;

class DailyInform extends MainAction
{
    public function display($resourceName, $parent_id = null)
    {
        $path = request()->path();

        //$path  = route('tickets.export');

        $filter = urlencode(TicketDailyInformFilter::class).'=daily-inform';

        $path .= '?filters='.base64_encode($filter);

        return "<a class='button btn btn-secondary float-right' href='{$path}'> <i class='fa fa-file-text'></i> Informe diario </a>";
    }
}
