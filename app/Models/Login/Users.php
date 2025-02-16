<?php

namespace App\Models\Login;

use PDO;
use App\Models\Model;

class Users extends Model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \App\Models\Database::getInstance()->getConnection();
    }

    public function login($login, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :login";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!isset($user['password'])) {
            throw new \Exception('Champ mot de passe invalide.');
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function register($data)
    {
        // Hachage du mot de passe avant l'insertion
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql = "INSERT INTO users (email, password, nom, prenom, role) VALUES (:email, :password, :nom, :prenom, :role)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        return $stmt->execute();
    }
}