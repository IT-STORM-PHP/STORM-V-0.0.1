<?php

    require_once dirname(__DIR__) . '/vendor/autoload.php';

    use App\Models\Database;

    try {
        $db = Database::getInstance()->getConnection();
        echo "Connexion réussie à la base de données !";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
?>