<?php

namespace App\Models;

use PDO;
use App\Models\Model;

class Articles extends Model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \App\Models\Database::getInstance()->getConnection();
    }
    public $id;
    public $nom;
    public $description;
    public $prix;
    public $quantite;
    public $created_at;
    public $updated_at;

    public function create($data)
    {
        $sql = "INSERT INTO articles (id, nom, description, prix, quantite, created_at, updated_at) VALUES (:id, :nom, :description, :prix, :quantite, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':prix', $data['prix']);
        $stmt->bindParam(':quantite', $data['quantite']);
        $stmt->bindParam(':created_at', $data['created_at']);
        $stmt->bindParam(':updated_at', $data['updated_at']);
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM articles";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function read($id)
    {
        $sql = "SELECT * FROM articles WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE articles SET id = :id, nom = :nom, description = :description, prix = :prix, quantite = :quantite, created_at = :created_at, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':prix', $data['prix']);
        $stmt->bindParam(':quantite', $data['quantite']);
        $stmt->bindParam(':created_at', $data['created_at']);
        $stmt->bindParam(':updated_at', $data['updated_at']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
