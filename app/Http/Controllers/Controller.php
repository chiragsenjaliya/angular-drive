<?php

namespace App\Http\Controllers;

use App\Components\Core\ResponseHelpers;
use App\Components\Core\FileHelpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,ResponseHelpers,FileHelpers;
}
