<?php

namespace App\Controllers;

use App\Models\Datetimes;
use App\Views\View;
use App\Http\Request;
class DatetimesController extends Controller
{
    private $model, $request;
    public function __construct()
    {
        $this->model = new Datetimes();
        $this->request = new Request();
    }

    public function index()
    {
        // Logique pour afficher la liste
        $items = $this->model->getAll();
        return View::render('datetimes/index', ['items' => $items]);
    }
    public function show($id)
    {
        // Logique pour afficher un élément
        $item = $this->model->read($id);
        return View::render('datetimes/show', ['item' => $item]);
    }
    public function create()
    {
        // Logique pour afficher le formulaire de création
        return View::render('datetimes/create');
    }
    public function store()
    {
        // Logique pour enregistrer l'élément
        $data = [
            'id' => $this->request->get('id'),
            'codArticle' => $this->request->get('codArticle'),
            'nomArticle' => $this->request->get('nomArticle'),
            'created_at' => $this->request->get('created_at'),
            'updated_at' => $this->request->get('updated_at'),
        ];
        $this->model->create($data);
        return View::redirect('/datetimes');
    }
    public function edit($id)
    {
        // Logique pour afficher le formulaire de modification
       $item = $this->model->read($id);
       return View::render('datetimes/edit', ['item' => $item]);
    }
    public function update($id)
    {
        // Logique pour mettre à jour l'élément
        $data = [
            'id' => $this->request->get('id'),
            'codArticle' => $this->request->get('codArticle'),
            'nomArticle' => $this->request->get('nomArticle'),
            'created_at' => $this->request->get('created_at'),
            'updated_at' => $this->request->get('updated_at'),
        ];
        $this->model->update($id, $data);
        return View::redirect('/datetimes');
    }
    public function destroy($id)
    {
        // Logique pour supprimer l'élément
        $this->model->delete($id);
        return View::redirect('/datetimes');
    }
}
