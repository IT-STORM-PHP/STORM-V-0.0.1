<?php

namespace App\Controllers\Login;

use App\Models\Login\Users;
use App\Controllers\Controller;
use App\Http\Request;
use App\Views\View;
class LoginController extends Controller
{
    public $username;
    public $nom;
    public $prenom;
    public $email;
    public $password;

    private $model;
    private $request;
    
    public function __construct(){
    $this->model = new Users();
        $this->request = new Request();
        $this->username = $this->request->get('username');
        $this->nom = $this->request->get('nom');
        $this->prenom = $this->request->get('prenom');
        $this->email = $this->request->get('email');
        $this->password = $this->request->get('password');

    }

    public function loginpage(){
        return View::render('sessions/login');
    }
    public function registerpage(){
        return View::render('sessions/register');
    }

    public function login()
    {
        // Vérifier la présence des champs essentiels
        if (empty($this->username) || empty($this->password)) {
            echo 'Veuillez fournir un identifiant et un mot de passe.';
            return;
        }

        // Appeler le modèle pour vérifier les identifiants
        $user = $this->model->login($this->username, $this->password);
        
        if ($user) {
            
            $_SESSION['user'] = $user;
            echo "Bienvenue " . $user['username'];
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
        if (empty($this->username) || empty($this->password)) {
            echo 'Veuillez fournir un identifiant et un mot de passe.';
            return;
        }

        // Appeler la méthode register du modèle pour enregistrer l'utilisateur
        $user = $this->model->register($data);
        
        if ($user) {
            
            $_SESSION['user'] = $user;

            echo '<div class="alert alert-success" role="alert">
                    Enregistrement effectué avec succès. Veillez vous connecter.
                    </div>
                ';
            return View::redirect('/login/page');
        } else {
             echo '<div class="alert alert-danger" role="alert">
                    Erreur lors de l\'enregistrement.
                    </div>
                ';
        }
    }
}
