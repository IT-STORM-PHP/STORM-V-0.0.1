<?php

namespace App\Console\Commands;

use App\Models\Database;
use PDO;

class MakeLogin
{
    public static function run()
    {
        echo "\033[32mQuelle table voulez-vous utiliser pour l'authentification ? \033[0m";
        $table = trim(fgets(STDIN));

        // Connexion à la base de données
        $pdo = Database::getInstance()->getConnection();

        // Vérifier si la table existe
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if (!$stmt->fetch()) {
            echo "\033[31mErreur : La table '$table' n'existe pas.\033[0m\n";
            return;
        }

        // Récupérer les champs de la table
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Table trouvée avec les champs suivants :\n";
        foreach ($columns as $col) {
            echo " - {$col['Field']}\n";
        }

        // Identifier la clé primaire
        $primaryKey = null;
        foreach ($columns as $col) {
            if ($col['Key'] === 'PRI') {
                $primaryKey = $col['Field'];
                break;
            }
        }

        // Demander le champ de login
        echo "\033[32mQuel champ utiliser pour le login ? \033[0m";
        $loginField = trim(fgets(STDIN));
        if (!in_array($loginField, array_column($columns, 'Field'))) {
            echo "\033[31mErreur : Le champ '$loginField' n'existe pas dans la table.\033[0m\n";
            return;
        }

        // Demander le champ de mot de passe
        echo "\033[32mQuel champ utiliser pour le mot de passe ? \033[0m";
        $passwordField = trim(fgets(STDIN));
        if (!in_array($passwordField, array_column($columns, 'Field'))) {
            echo "\033[31mErreur : Le champ '$passwordField' n'existe pas dans la table.\033[0m\n";
            return;
        }

        // Exclure les champs non nécessaires pour l'inscription
        $excludedFields = ['created_at', 'updated_at'];
        if ($primaryKey) {
            $excludedFields[] = $primaryKey;
        }

        // Exclure les colonnes commençant par "id"
        foreach ($columns as $col) {
            if (strpos($col['Field'], 'id') === 0) {
                $excludedFields[] = $col['Field'];
            }
        }

        // Définir les champs à insérer
        $insertFields = array_diff(array_column($columns, 'Field'), $excludedFields);
        $insertFieldsString = implode(', ', $insertFields);
        $insertPlaceholders = ':' . implode(', :', $insertFields);

        // Génération du modèle
        $modelContent = "<?php

namespace App\\Models\\Login;

use PDO;
use App\\Models\\Model;

class " . ucfirst($table) . " extends Model
{
    private \$pdo;

    public function __construct()
    {
        \$this->pdo = \\App\\Models\\Database::getInstance()->getConnection();
    }

    public function login(\$login, \$password)
    {
        \$sql = \"SELECT * FROM $table WHERE $loginField = :login\";
        \$stmt = \$this->pdo->prepare(\$sql);
        \$stmt->bindParam(':login', \$login);
        \$stmt->execute();
        \$user = \$stmt->fetch(PDO::FETCH_ASSOC);

        if (!\$user) {
            return false;
        }

        if (!isset(\$user['$passwordField'])) {
            throw new \\Exception('Champ mot de passe invalide.');
        }

        if (password_verify(\$password, \$user['$passwordField'])) {
            return \$user;
        }
        return false;
    }

    public function register(\$data)
    {
        // Hachage du mot de passe avant l'insertion
        if (isset(\$data['$passwordField'])) {
            \$data['$passwordField'] = password_hash(\$data['$passwordField'], PASSWORD_BCRYPT);
        }

        \$sql = \"INSERT INTO $table ($insertFieldsString) VALUES ($insertPlaceholders)\";
        \$stmt = \$this->pdo->prepare(\$sql);
        foreach (\$data as \$key => \$value) {
            \$stmt->bindValue(':' . \$key, \$value);
        }
        return \$stmt->execute();
    }
}";

        $modelDir = __DIR__ . "/../../Models/Login/";
        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0777, true);
        }
        $modelPath = $modelDir . ucfirst($table) . ".php";
        file_put_contents($modelPath, $modelContent);
        echo "\033[32mModèle généré : $modelPath\033[0m\n";

        // Génération du contrôleur avec attributs dynamiques
        $controllerAttributes = "";
        $controllerAssignments = "";
        foreach ($columns as $col) {
            $field = $col['Field'];
            // Exclure les champs non nécessaires
            if (!in_array($field, $excludedFields)) {
                $controllerAttributes .= "    public \$$field;\n";
                $controllerAssignments .= "        \$this->$field = \$this->request->get('$field');\n";
            }
        }

        $controllerContent = "<?php

namespace App\\Controllers\\Login;

use App\\Models\\Login\\" . ucfirst($table) . ";
use App\\Controllers\\Controller;
use App\\Http\\Request;
use App\\Views\\View;
class LoginController extends Controller
{
$controllerAttributes
    private \$model;
    private \$request;
    
    public function __construct(){
    \$this->model = new " . ucfirst($table) . "();
        \$this->request = new Request();
$controllerAssignments
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
        if (empty(\$this->$loginField) || empty(\$this->$passwordField)) {
            echo 'Veuillez fournir un identifiant et un mot de passe.';
            return;
        }

        // Appeler le modèle pour vérifier les identifiants
        \$user = \$this->model->login(\$this->$loginField, \$this->$passwordField);
        
        if (\$user) {
            
            \$_SESSION['user'] = \$user;
            echo \"Bienvenue \" . \$user['$loginField'];
        } else {
            echo 'Identifiants incorrects';
        }
    }

    public function register()
    {
        \$data = [];
        
        // Remplir dynamiquement les champs à partir des attributs du contrôleur
        foreach (get_object_vars(\$this) as \$key => \$value) {
            if (!in_array(\$key, ['model', 'request'])) {
                \$data[\$key] = \$value;
            }
        }

        // Vérifier la présence des champs essentiels
        if (empty(\$this->$loginField) || empty(\$this->$passwordField)) {
            echo 'Veuillez fournir un identifiant et un mot de passe.';
            return;
        }

        // Appeler la méthode register du modèle pour enregistrer l'utilisateur
        \$user = \$this->model->register(\$data);
        
        if (\$user) {
            
            \$_SESSION['user'] = \$user;

            echo '<div class=\"alert alert-success\" role=\"alert\">
                    Enregistrement effectué avec succès. Veillez vous connecter.
                    </div>
                ';
            return View::redirect('/login/page');
        } else {
             echo '<div class=\"alert alert-danger\" role=\"alert\">
                    Erreur lors de l\'enregistrement.
                    </div>
                ';
        }
    }
}
";      //Génération du fichiers de login




        $controllerDir = __DIR__ . "/../../Controllers/Login/";
        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0777, true);
        }

        file_put_contents($controllerDir . "LoginController.php", $controllerContent);
        echo "\033[32mContrôleur généré : $controllerDir" . "LoginController.php\033[0m\n";

        // Ajout des routes dans web.php
        $webPath = __DIR__ . "/../../../routes/web.php";
        $webContent = file_get_contents($webPath);

        // Ajout des imports à la fin du fichier
        if (strpos($webContent, 'use App\\Controllers\\Login\\LoginController;') === false) {
            $webContent .= "\nuse App\\Controllers\\Login\\LoginController;";
        }

        // Ajout des routes si elles n'existent pas déjà
        if (strpos($webContent, 'Route::post(\'/login\', [LoginController::class, \'login\']);') === false) {
            $webContent .= "\nRoute::post('/login', [LoginController::class, 'login']);";
        }

        if (strpos($webContent, 'Route::post(\'/register\', [LoginController::class, \'register\']);') === false) {
            $webContent .= "\nRoute::post('/register', [LoginController::class, 'register']);";
        }

        if (strpos($webContent, 'Route::get(\'/register/page\', [LoginController::class, \'registerpage\']);') === false) {
            $webContent .= "\nRoute::get('/register/page', [LoginController::class, 'registerpage']);";
        }
        if (strpos($webContent, 'Route::get(\'/login/page\', [LoginController::class, \'loginpage\']);') === false) {
            $webContent .= "\nRoute::get('/login/page', [LoginController::class, 'loginpage']);";
        }

        file_put_contents($webPath, $webContent);
        echo "\033[32mRoutes ajoutées dans : $webPath\033[0m\n";

        // Générer la vue pour la connexion
        function readTemplate($path, $model = null)
        {
            $content = file_get_contents(__DIR__ . "../../../../public/{$path}.php");

            
            if ($model !== null) {
                $content = str_replace('{$model}', $model, $content);
            }

            return $content;
        }
        $htmlHeader = readTemplate('_header', 'Authentification');
        $htmlFooter = readTemplate('_footer', 'Authentification');

        $loginFieldToUpper = ucfirst($loginField);
        $loginViewContent = "<?php\n"
    . "\$_SESSION['errors'] = [];\n"
    . "?>\n"
    . $htmlHeader
    . "<div class='container d-flex justify-content-center align-items-center vh-100'>\n"
    . "    <div class='card shadow-lg' style='max-width: 500px; width: 100%;'>\n"
    . "        <div class='card-header bg-primary text-white text-center'>\n"
    . "            <h4>Connexion</h4>\n"
    . "        </div>\n"
    . "        <div class='card-body'>\n"
    . "            <form action='/login' method='post'>\n"
    . "                <div class='mb-3'>\n"
    . "                    <label for='$loginField' class='form-label'>$loginFieldToUpper</label>\n"
    . "                    <input type='text' name='$loginField' class='form-control' id='$loginField' required>\n"
    . "                </div>\n"
    . "                <div class='mb-3'>\n"
    . "                    <label for='$passwordField' class='form-label'>Mot de passe</label>\n"
    . "                    <input type='password' name='$passwordField' class='form-control' id='$passwordField' required>\n"
    . "                </div>\n"
    . "                <button type='submit' class='btn btn-primary w-100 py-2'>Se connecter</button>\n"
    . "            </form>\n"
    . "        </div>\n"
    . "    </div>\n"
    . "</div>\n"
    . $htmlFooter;

        // Générer la vue pour l'inscription
$registerViewContent = "<?php\n"
. "\$_SESSION['errors'] = [];\n"
. "?>\n"
. $htmlHeader
. "<div class='container d-flex justify-content-center align-items-center vh-100'>\n"
. "<div class='card shadow-lg' style='max-width: 500px; width: 100%;'>\n"
. "    <div class='card-header bg-primary text-white text-center'>\n"
. "        <h4>Inscription</h4>\n"
. "    </div>\n"
. "    <div class='card-body'>\n"
. "        <form action='/register' method='post'>\n";

// Ajouter les champs du formulaire
foreach ($columns as $col) {
$field = $col['Field'];
if (!in_array($field, $excludedFields)) {
    $registerViewContent .= "            <div class='mb-3'>\n"
        . "                <label for='$field' class='form-label'>$field</label>\n"
        . "                <input type='text' name='$field' class='form-control' required>\n"
        . "            </div>\n";
}
}

$registerViewContent .= "            <button type='submit' class='btn btn-primary w-100 py-2'>S'inscrire</button>\n"
. "        </form>\n"
. "    </div>\n"
. "</div>\n"
. "</div>\n"
. $htmlFooter;

        // Répertoire pour les vues
        $viewsDir = __DIR__ . "/../../Views/sessions/";

        // Créer le répertoire si nécessaire
        if (!is_dir($viewsDir)) {
            mkdir($viewsDir, 0777, true);
        }

        // Générer les fichiers de vue
        file_put_contents($viewsDir . "login.php", $loginViewContent);
        file_put_contents($viewsDir . "register.php", $registerViewContent);

        echo "\033[32mVues générées dans : $viewsDir\033[0m\n";
    }
}

