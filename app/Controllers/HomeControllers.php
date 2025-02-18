<?php

namespace App\Controllers;


use App\Views\View;

class HomeControllers extends Controller
{
        public function index()
    {
        // Rendu de la vue via View::render (simplification de l'usage)
        return View::render('/home/index', []);
    }

       
}
