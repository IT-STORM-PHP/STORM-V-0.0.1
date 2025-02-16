<?php

namespace App\Controllers\Login;

use App\Models\Login\Users;
use App\Controllers\Controller;
use App\Http\Request;

class LoginController extends Controller
{
    public $email;
    public $password;
    public $nom;
    public $prenom;
    public $role;

    private $model;
    private $request;
    
    public function __construct(){
    $this->model = new Users();
        $this->request = new Request();
        $this->email = $this->request->get('email');
        $this->password = $this->request->get('password');
        $this->nom = $this->request->get('nom');
        $this->prenom = $this->request->get('prenom');
        $this->role = $this->request->get('role');

    }

    public function login()
    {
        // Vérifier la présence des champs essentiels
        if (empty($this->email) || empty($this->password)) {
            echo 'Veuillez fournir un identifiant et un mot de passe.';
            return;
        }

        // Appeler le modèle pour vérifier les identifiants
        $user = $this->model->login($this->email, $this->password);
        
        if ($user) {
            session_start();
            $_SESSION['user'] = $user;
            echo "Bienvenue " . $user['email'];
        } else {
            echo 'Identifiants incorrects';
        }
    }

    public function register()
    {
        $data = [];
        
        // Remplir dynamiquement les champs à partir des attributs du contrôleur
        foreach (get_object_vars($this) as $key => $value) {
            if (!in_array($key, ['model', 'request'])) {
                $data[$key] = $value;
            }
        }

        // Vérifier la présence des champs essentiels
        if (empty($this->email) || empty($this->password)) {
            echo 'Veuillez fournir un identifiant et un mot de passe.';
            return;
        }

        // Appeler la méthode register du modèle pour enregistrer l'utilisateur
        $user = $this->model->register($data);
        
        if ($user) {
            session_start();
            $_SESSION['user'] = $user;
            echo 'Enregistrement effectué avec succès';
        } else {
            echo 'Erreur lors de l\'enregistrement.';
        }
    }
}
