<?php

namespace App\Http\Controllers;

use BadChoice\Thrust\Controllers\ThrustController;

class TicketPostTypesController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('ticketPostTypes');
    }
}
