<?php

namespace App\Repositories;

use App\Attachment;
use Carbon\Carbon;

class TicketAttachmentsRepository
{
    public function empty()
    {
        return Attachment::whereNull('created_at')->orderBy('attachments.created_at','DESC');
    }

    public function all()
    {
        return Attachment::whereNotNull('created_at')->orderBy('attachments.created_at','DESC');
    }

    public function search($text)
    {
        $leadsQuery = auth()->user()->admin ? Ticket::query() : auth()->user()->teamsTickets();

        return $leadsQuery->where(function ($query) use ($text) {
            $query->where('title', 'like', "%{$text}%")->orWhere('body', 'like', "%{$text}%");
        });
    }
}
