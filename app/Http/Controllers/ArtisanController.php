<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Artisan;

class ArtisanController extends Controller
{
    public function cacheClear()
    {
         dump(Artisan::call('config:clear'));
    }
}
