<?php

namespace App\Controllers;

use App\Controller\Controllers;
use App\Views\View;
class HomeControllers extends Controller
{
    public function index()
    {
        return View::render('/home/index', []);
    }
}
