<?php

namespace App\Http\Controllers;

use BadChoice\Thrust\Controllers\ThrustController;

class ChannelsController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('channels');
    }
}
