<?php

namespace App\Http\Controllers;

use App\Repositories\TicketsIndexQuery;
use App\Repositories\TicketsRepository;
use App\Ticket;
use App\Attachment;
use BadChoice\Thrust\Controllers\ThrustController;

class TicketAttachmentsController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('ticketAttachments');
    }

}
