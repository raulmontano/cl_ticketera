<?php

namespace App\Http\Controllers;

use App\Repositories\TicketsIndexQuery;
use App\Repositories\TicketsRepository;
use App\Ticket;
use App\Attachment;
use BadChoice\Thrust\Controllers\ThrustController;
use Illuminate\Support\Facades\Gate;

class TicketsDashboard extends Controller
{
    public function index()
    {

        if(Gate::allows('see-dashboard')){
            return view('tickets.dashboard');
        } else {
            abort(401);
        }
        
    }

}
