<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPostType extends BaseModel
{
    use SoftDeletes;

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function delete()
    {
        $this->tickets()->update(['ticket_post_type_id' => null]);

        return parent::delete();
    }
}
