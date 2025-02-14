<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Models\Database;

try {
    // Tentative de connexion à la base de données
    $db = Database::getInstance()->getConnection();
    echo "✅ Connexion réussie à la base de données !";
} catch (\PDOException $e) {
    $errorCode = $e->getCode();
    $errorMessage = '';

    switch ($errorCode) {
        case 1049:
            $errorMessage = "❌ Erreur : La base de données spécifiée n'existe pas.";
            break;
        case 1045:
            $errorMessage = "❌ Erreur : Identifiant ou mot de passe incorrect.";
            break;
        case 2002:
            $errorMessage = "❌ Erreur : Impossible de se connecter au serveur MySQL. Vérifiez si MySQL est démarré.";
            break;
        default:
            $errorMessage = "❌ Erreur : Un problème est survenu avec la base de données.";
    }

    error_log("Erreur PDO ({$errorCode}) : " . $e->getMessage()); // Journalisation de l'erreur
    echo $errorMessage;
} catch (\Exception $e) {
    error_log("Erreur générale : " . $e->getMessage()); // Journalisation
    echo "❌ Erreur : Une erreur interne est survenue. Contactez l'administrateur.";
}

?>
