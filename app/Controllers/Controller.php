<?php

namespace App\Controllers;


class Controller
{
    // Méthode pour obtenir l'instance du modèle
    public static function model($model)
    {
        $modelName = "App\\Models\\$model";
        return new $modelName();
    }

    // Méthode pour gérer la réponse (par exemple, envoyer des données JSON)
    public static function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Méthode pour afficher une vue (si tu utilises un moteur de template)
    public static function renderView($view, $data = [])
    {
        // Exemple simple de rendu de vue sans moteur de template
        extract($data); // Créer des variables à partir du tableau $data
        require "app/Views/$view.php";
    }
}
