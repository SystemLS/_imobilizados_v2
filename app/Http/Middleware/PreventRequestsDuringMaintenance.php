<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Closure;
use Illuminate\Http\Request;

class PreventRequestsDuringMaintenance extends Middleware
{
    // Este middleware é usado automaticamente pelo Laravel durante o modo de manutenção
}
