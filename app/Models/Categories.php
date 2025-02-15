<?php

namespace App\Models;

use PDO;
use App\Models\Model;

class Categories extends Model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \App\Models\Database::getInstance()->getConnection();
    }
    public $id;
    public $nom_categorie;
    public $description;
    public $slug;
    public $created_at;
    public $updated_at;

    public function create($data)
    {
        $sql = "INSERT INTO categories (id, nom_categorie, description, slug, created_at, updated_at) VALUES (:id, :nom_categorie, :description, :slug, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':nom_categorie', $data['nom_categorie']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':created_at', $data['created_at']);
        $stmt->bindParam(':updated_at', $data['updated_at']);
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function read($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE categories SET id = :id, nom_categorie = :nom_categorie, description = :description, slug = :slug, created_at = :created_at, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':nom_categorie', $data['nom_categorie']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':created_at', $data['created_at']);
        $stmt->bindParam(':updated_at', $data['updated_at']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
