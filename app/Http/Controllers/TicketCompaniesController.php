<?php

namespace App\Http\Controllers;

use BadChoice\Thrust\Controllers\ThrustController;

class TicketCompaniesController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('ticketCompanies');
    }
}
