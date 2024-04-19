<?php

namespace App\Repositories;

use App\Filters\TicketFilters;

class TicketAttachmentsIndexQuery
{
    public static function get(TicketAttachmentsRepository $repository = null)
    {
        if (! $repository) {
            $repository = app(TicketAttachmentsRepository::class);
        }

        if (request('default')) {
            $attachments = $repository->empty();
        } else {
            $attachments = $repository->all();
        }

        $attachments = (new TicketFilters())->apply($attachments, request()->all());

        return $attachments;
    }
}
