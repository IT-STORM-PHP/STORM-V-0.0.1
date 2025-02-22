<?php

namespace App\Models;

use PDO;
use App\Models\Model;

date_default_timezone_set('GMT');

class Livres extends Model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \App\Models\Database::getInstance()->getConnection();
    }
    public $id;
    public $titre;
    public $annee_publication;
    public $auteur_id;
    public $categorie_id;
    public $lieu_edition_id;
    public $verifie;
    public $archive;

    public function create($data)
    {
        $sql = "INSERT INTO livres (id, titre, annee_publication, auteur_id, categorie_id, lieu_edition_id, verifie, archive) VALUES (:id, :titre, :annee_publication, :auteur_id, :categorie_id, :lieu_edition_id, :verifie, :archive)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':titre', $data['titre']);
        $stmt->bindValue(':annee_publication', $data['annee_publication']);
        $stmt->bindValue(':auteur_id', $data['auteur_id']);
        $stmt->bindValue(':categorie_id', $data['categorie_id']);
        $stmt->bindValue(':lieu_edition_id', $data['lieu_edition_id']);
        $stmt->bindValue(':verifie', $data['verifie']);
        $stmt->bindValue(':archive', $data['archive']);
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM livres";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function read($id)
    {
        $sql = "SELECT * FROM livres WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE livres SET titre = :titre, annee_publication = :annee_publication, auteur_id = :auteur_id, categorie_id = :categorie_id, lieu_edition_id = :lieu_edition_id, verifie = :verifie, archive = :archive WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titre', $data['titre']);
        $stmt->bindValue(':annee_publication', $data['annee_publication']);
        $stmt->bindValue(':auteur_id', $data['auteur_id']);
        $stmt->bindValue(':categorie_id', $data['categorie_id']);
        $stmt->bindValue(':lieu_edition_id', $data['lieu_edition_id']);
        $stmt->bindValue(':verifie', $data['verifie']);
        $stmt->bindValue(':archive', $data['archive']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM livres WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
