<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCompany extends BaseModel
{
    use SoftDeletes;

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function delete()
    {
        $this->tickets()->update(['ticket_company_id' => null]);

        return parent::delete();
    }
}
