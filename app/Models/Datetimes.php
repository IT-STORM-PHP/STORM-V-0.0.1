<?php

namespace App\Models;

use PDO;
use App\Models\Model;

date_default_timezone_set('GMT');

class Datetimes extends Model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \App\Models\Database::getInstance()->getConnection();
    }
    public $id;
    public $codAricle;
    public $nomArticle;
    public $created_at;
    public $updated_at;

    public function create($data)
    {
        $sql = "INSERT INTO datetimes (id, codAricle, nomArticle, created_at, updated_at) VALUES (:id, :codAricle, :nomArticle, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':codAricle', $data['codAricle']);
        $stmt->bindParam(':nomArticle', $data['nomArticle']);
       $stmt->bindParam(':created_at', date('Y-m-d H:i:s'));
       $stmt->bindParam(':updated_at', date('Y-m-d H:i:s'));
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM datetimes";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function read($id)
    {
        $sql = "SELECT * FROM datetimes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE datetimes SET id = :id, codAricle = :codAricle, nomArticle = :nomArticle, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':codAricle', $data['codAricle']);
        $stmt->bindParam(':nomArticle', $data['nomArticle']);
        $stmt->bindParam(':updated_at', date('Y-m-d H:i:s'));
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM datetimes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
