<?php

namespace App\Http\Controllers;

use BadChoice\Thrust\Controllers\ThrustController;

class CategoriesController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('categories');
    }
}
