<?php

namespace App\Models;

class Model
{
    // Connexion à la base de données via la classe Database
    protected static function getConnection()
    {
        return Database::getInstance()->getConnection();
    }

    // Méthode pour exécuter une requête générique
    protected static function executeQuery($sql, $params = [])
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
