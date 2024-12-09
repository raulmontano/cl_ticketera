<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;

class AgentRepository
{
    public function all()
    {
        return User::where('admin', 0);
    }
}
