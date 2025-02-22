<?php

namespace App\Controllers;

use App\Models\Livres;
use App\Views\View;
use App\Http\Request;
use App\Models\Database;
use PDO;

class LivresController extends Controller
{
    private $model, $request, $pdo, $foreignKeys;
    public function __construct()
    {
        $this->model = new Livres();
        $this->request = new Request();
        $this->pdo = Database::getInstance()->getConnection();
        $this->foreignKeys = array (
  'auteur_id' => 
  array (
    'table' => 'auteurs',
    'column' => 'id',
    'display_column' => 'nom',
  ),
  'categorie_id' => 
  array (
    'table' => 'categories',
    'column' => 'id',
    'display_column' => 'nom',
  ),
  'lieu_edition_id' => 
  array (
    'table' => 'lieu_edition',
    'column' => 'id',
    'display_column' => 'nom',
  ),
);
    }

    public function index()
    {
        try {
            // Récupérer tous les éléments
            $items = $this->model->getAll();

            // Récupérer les données associées pour toutes les clés étrangères
            foreach ($this->foreignKeys as $column => $foreignKey) {
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $displayColumn = $foreignKey['display_column'] ?? $foreignColumn; // Utiliser la colonne significative

                // Récupérer toutes les données de la table étrangère
                $stmt = $this->pdo->query("SELECT * FROM " . $foreignTable);
                $foreignData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Associer les données étrangères aux éléments
                foreach ($items as &$item) {
                    foreach ($foreignData as $foreignItem) {
                        if ($item[$column] == $foreignItem[$foreignColumn]) {
                            $item[$foreignKey['table'] . '_' . $displayColumn] = $foreignItem[$displayColumn] ?? 'N/A';
                            break;
                        }
                    }
                }
            }

            return View::render('livres/index', ['items' => $items]);
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {
            // Récupérer l'élément spécifique
            $item = $this->model->read($id);
            if (!$item) {
                return View::render('error', ['message' => 'Livres not found']);
            }

            // Récupérer les données associées pour toutes les clés étrangères
            foreach ($this->foreignKeys as $column => $foreignKey) {
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $displayColumn = $foreignKey['display_column'] ?? $foreignColumn; // Utiliser la colonne significative

                // Récupérer les données de la table étrangère
                $stmt = $this->pdo->prepare("SELECT * FROM " . $foreignTable . " WHERE " . $foreignColumn . " = ?");
                $stmt->execute([$item[$column]]);
                $foreignItem = $stmt->fetch(PDO::FETCH_ASSOC);

                // Associer les données étrangères à l'élément
                $item[$foreignKey['table'] . '_' . $displayColumn] = $foreignItem[$displayColumn] ?? 'N/A';
            }

            return View::render('livres/show', ['item' => $item]);
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM auteurs");
            $auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $this->pdo->query("SELECT * FROM categories");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $this->pdo->query("SELECT * FROM lieu_edition");
            $lieu_edition = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return View::render('livres/create', [
                'auteurs' => $auteurs,
                'categories' => $categories,
                'lieu_edition' => $lieu_edition,
            ]);
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
    public function store()
    {
        try {
            $data = [
                'id' => $this->request->get('id'),
                'titre' => $this->request->get('titre'),
                'annee_publication' => $this->request->get('annee_publication'),
                'auteur_id' => $this->request->get('auteur_id'),
                'categorie_id' => $this->request->get('categorie_id'),
                'lieu_edition_id' => $this->request->get('lieu_edition_id'),
                'verifie' => $this->request->get('verifie'),
                'archive' => $this->request->get('archive'),
            ];
            $this->model->create($data);
            return View::redirect('/livres');
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {
            $item = $this->model->read($id);
            if (!$item) {
                return View::render('error', ['message' => 'Livres not found']);
            }
            $stmt = $this->pdo->query("SELECT * FROM auteurs");
            $auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $this->pdo->query("SELECT * FROM categories");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $this->pdo->query("SELECT * FROM lieu_edition");
            $lieu_edition = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return View::render('livres/edit', [
                'item' => $item,
                'auteurs' => $auteurs,
                'categories' => $categories,
                'lieu_edition' => $lieu_edition,
            ]);
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
    public function update($id)
    {
        try {
            $data = [
                'id' => $this->request->get('id'),
                'titre' => $this->request->get('titre'),
                'annee_publication' => $this->request->get('annee_publication'),
                'auteur_id' => $this->request->get('auteur_id'),
                'categorie_id' => $this->request->get('categorie_id'),
                'lieu_edition_id' => $this->request->get('lieu_edition_id'),
                'verifie' => $this->request->get('verifie'),
                'archive' => $this->request->get('archive'),
            ];
            $this->model->update($id, $data);
            return View::redirect('/livres');
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {
            $this->model->delete($id);
            return View::redirect('/livres');
        } catch (\Exception $e) {
            return View::render('error', ['message' => $e->getMessage()]);
        }
    }
}
