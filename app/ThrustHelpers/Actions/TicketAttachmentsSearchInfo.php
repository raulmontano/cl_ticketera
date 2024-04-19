<?php

namespace App\ThrustHelpers\Actions;

use BadChoice\Thrust\Actions\MainAction;
use App\Ticket;
use BadChoice\Thrust\ResourceFilters\Filters;

class TicketAttachmentsSearchInfo extends MainAction
{
    public function display($resourceName, $parent_id = null)
    {

      if(request()->filters){

        $filters = Filters::decodeFilters(request()->filters);

        if($filters->count()){

            $withOutEmpties = $filters->filter(function ($value) {
                  return !empty($value);
            });

            $filtersTxt = implode(',',$withOutEmpties->toArray());

            return "<h3 class='mt-3'>Resultados de la busqueda '". $filtersTxt."'</h3>";
        } else {

        }
      }

      return "<div class='mt-3'>Realiza una busqueda de archivos por 'Asunto' de solicitud</div>";
        //dd($filters->first());



    }
}
