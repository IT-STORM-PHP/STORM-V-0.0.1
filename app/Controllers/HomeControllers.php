<?php

namespace App\Controllers;

use App\Controller\Controllers;
use App\Views\View;
class HomeControllers extends Controller
{
    public function index()
    {
        // Action par défaut
        $data = [
            'title' => 'Accueil',
            'description' => 'Bienvenue sur STORM MVC',
            
        ];
        
        return View::render('home/index');
    }
}
