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
            'content' => 'Bienvenue sur le site de la formation PHP'
            
        ];
        return View::jsonResponse($data);
    }
}
