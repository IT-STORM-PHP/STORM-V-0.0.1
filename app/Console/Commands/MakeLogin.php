<?php

namespace App\Console\Commands;

use App\Models\Database;
use PDOException;

class MakeLogin
{
    public function execute()
    {
        echo "\033[32m⚡ Génération du système d'authentification\033[0m\n";
        
        // Demander le nom de la table
        echo "Nom de la table utilisateur : ";
        $tableName = trim(fgets(STDIN));

        if (empty($tableName)) {
            echo "\033[31mErreur : Le nom de la table ne peut pas être vide.\033[0m\n";
            return;
        }

        // Demander les colonnes
        $columns = [];
        while (true) {
            echo "Nom de la colonne (Appuyez sur Entrée pour terminer) : ";
            $columnName = trim(fgets(STDIN));

            if (empty($columnName)) {
                break;
            }

            echo "Type de la colonne (int, varchar(255), text, etc.) : ";
            $columnType = trim(fgets(STDIN));

            echo "Est-ce le champ du mot de passe ? (oui/non) : ";
            $isPassword = strtolower(trim(fgets(STDIN))) === 'oui';

            echo "Est-ce que cette colonne doit être unique ? (oui/non) : ";
            $isUnique = strtolower(trim(fgets(STDIN))) === 'oui';

            $columns[] = [
                'name' => $columnName,
                'type' => $columnType,
                'is_password' => $isPassword,
                'is_unique' => $isUnique
            ];
        }

        // Demander la colonne pour la connexion (email ou username)
        echo "Quelle colonne sera utilisée pour se connecter (email, username, etc.) ? ";
        $loginColumn = trim(fgets(STDIN));

        // Demander la colonne pour le mot de passe
        echo "Quelle colonne sera utilisée pour le mot de passe ? ";
        $passwordColumn = trim(fgets(STDIN));

        // Création de la table immédiatement
        $this->createTable($tableName, $columns);
        
        // Génération des fichiers nécessaires
        $this->generateView($loginColumn, $passwordColumn);
        $this->generateModel($tableName, $columns);
        $this->generateController($tableName, $loginColumn, $passwordColumn);
        $this->generateRoutes($tableName);
        
        echo "\033[32m✅ Système de login généré avec succès !\033[0m\n";
    }

    private function createTable($tableName, $columns)
    {
        $db = Database::getInstance();
        $sql = "CREATE TABLE $tableName (id INT AUTO_INCREMENT PRIMARY KEY, ";
        
        foreach ($columns as $col) {
            // Vérifie que les types de colonnes sont bien définis
            if (strpos($col['type'], 'varchar') !== false && !strpos($col['type'], '(')) {
                $col['type'] = $col['type'] . "(255)";
            }

            // Ajoute la contrainte UNIQUE si nécessaire
            $columnSql = "{$col['name']} {$col['type']}";
            if ($col['is_unique']) {
                $columnSql .= " UNIQUE";
            }

            $sql .= $columnSql . ", ";
        }

        // Ajout de timestamps et autres colonnes spécifiques
        $sql .= "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";

        try {
            $result = $db->getConnection()->exec($sql);
            if ($result !== false) {
                echo "\033[32m✅ Table $tableName créée avec succès !\033[0m\n";
            } else {
                echo "\033[31m❌ Erreur lors de la création de la table !\033[0m\n";
            }
        } catch (PDOException $e) {
            echo "\033[31m❌ Erreur SQL : {$e->getMessage()}\nRequête SQL : $sql\033[0m\n";
        }
    }

    private function generateView($loginColumn, $passwordColumn)
    {
        // Vue de connexion
        $viewDir = "app/Views/login";
        if (!file_exists($viewDir)) {
            mkdir($viewDir, 0777, true); // Crée le dossier si il n'existe pas
        }

        // Formulaire de connexion
        $viewPath = $viewDir . "/login.php";
        if (!file_exists($viewPath)) {
            $loginInputType = ($loginColumn === 'email') ? 'email' : 'text';
            $formContent = "<h2>Connexion</h2>\n<form method='post' action='/login'>\n";
            $formContent .= "<input type='$loginInputType' name='$loginColumn' placeholder='" . ucfirst($loginColumn) . "' required>\n";
            $formContent .= "<input type='password' name='$passwordColumn' placeholder='Mot de passe' required>\n";
            $formContent .= "<button type='submit'>Se connecter</button>\n</form>";
            file_put_contents($viewPath, $formContent);
            echo "✅ Vue de connexion créée : $viewPath\n";
        }

        // Formulaire d'inscription
        $viewPath = $viewDir . "/register.php";
        if (!file_exists($viewPath)) {
            $registerContent = "<h2>Inscription</h2>\n<form method='post' action='/register'>\n";
            $registerContent .= "<input type='email' name='email' placeholder='Email' required>\n";
            $registerContent .= "<input type='password' name='password' placeholder='Mot de passe' required>\n";
            $registerContent .= "<button type='submit'>S'inscrire</button>\n</form>";
            file_put_contents($viewPath, $registerContent);
            echo "✅ Vue d'inscription créée : $viewPath\n";
        }

        // Formulaire de modification de mot de passe
        $viewPath = $viewDir . "/change_password.php";
        if (!file_exists($viewPath)) {
            $changePasswordContent = "<h2>Changer le mot de passe</h2>\n<form method='post' action='/change-password'>\n";
            $changePasswordContent .= "<input type='password' name='current_password' placeholder='Mot de passe actuel' required>\n";
            $changePasswordContent .= "<input type='password' name='new_password' placeholder='Nouveau mot de passe' required>\n";
            $changePasswordContent .= "<button type='submit'>Changer</button>\n</form>";
            file_put_contents($viewPath, $changePasswordContent);
            echo "✅ Vue de changement de mot de passe créée : $viewPath\n";
        }
    }

    private function generateModel($tableName, $columns)
    {
        $modelPath = "app/Models/" . ucfirst($tableName) . ".php";
        $columnsSql = '';

        foreach ($columns as $col) {
            $columnsSql .= "\n    // Code pour le champ : " . $col['name'] . "\n";
        }

        // Création du modèle avec les méthodes CRUD
        $modelContent = "<?php\n\nnamespace App\Models;\n\nclass " . ucfirst($tableName) . " {\n";
        $modelContent .= "    // Définir les propriétés : table, champs\n";
        $modelContent .= "    protected \$table = '$tableName';\n";
        $modelContent .= "    protected \$fillable = ['" . implode("', '", array_column($columns, 'name')) . "'];\n";
        $modelContent .= "    protected \$timestamps = true;\n";
        
        // Méthode `where`
        $modelContent .= "\n    public static function where(\$column, \$operator, \$value) {\n";
        $modelContent .= "        // Exemple de requête personnalisée\n";
        $modelContent .= "        \$query = 'SELECT * FROM ' . static::\$table . ' WHERE ' . \$column . ' ' . \$operator . ' ?';\n";
        $modelContent .= "        return Database::getInstance()->getConnection()->prepare(\$query)->execute([\$value]);\n";
        $modelContent .= "    }\n";
        
        $modelContent .= "}\n";

        file_put_contents($modelPath, $modelContent);
        echo "✅ Modèle créé : $modelPath\n";
    }

    private function generateController($tableName, $loginColumn, $passwordColumn)
    {
        $controllerPath = "app/Controllers/" . ucfirst($tableName) . "Controller.php";
        $controllerContent = "<?php\n\nnamespace App\Controllers;\n\nuse App\Models\\" . ucfirst($tableName) . ";\nuse PDOException;\n\nclass " . ucfirst($tableName) . "Controller {\n";

        // Connexion de l'utilisateur
        $controllerContent .= "    public function login() {\n";
        $controllerContent .= "        if (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n";
        $controllerContent .= "            // Récupérer les données envoyées\n";
        $controllerContent .= "            \$login = \$_POST['$loginColumn'];\n";
        $controllerContent .= "            \$password = \$_POST['$passwordColumn'];\n\n";
        $controllerContent .= "            // Vérifier les informations de connexion\n";
        $controllerContent .= "            \$user = " . ucfirst($tableName) . "::where('$loginColumn', '=', \$login)->first();\n";
        $controllerContent .= "            if (\$user && password_verify(\$password, \$user->$passwordColumn)) {\n";
        $controllerContent .= "                // Connexion réussie\n";
        $controllerContent .= "                echo 'Connexion réussie';\n";
        $controllerContent .= "            } else {\n";
        $controllerContent .= "                // Erreur de connexion\n";
        $controllerContent .= "                echo 'Erreur de connexion';\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";

        $controllerContent .= "}\n";

        file_put_contents($controllerPath, $controllerContent);
        echo "✅ Contrôleur créé : $controllerPath\n";
    }

    private function generateRoutes($tableName)
    {
        $routePath = "routes/web.php";
        $tableNameToUpper = ucfirst($tableName);
        // Routes : Utilisation du format correct
        $routesContent = file_get_contents($routePath);
        $routesContent .= "\n// Routes d'authentification\n";
        $routesContent .= "use App\Controllers\\" . ucfirst($tableName) . "Controller;\n";
        $routesContent .= "Route::get('/login', [{$tableNameToUpper}Controller::class, 'login']);\n";
        $routesContent .= "Route::post('/login', [{$tableNameToUpper}Controller::class, 'login']);\n";
        $routesContent .= "Route::get('/register', [{$tableNameToUpper}Controller::class, 'register']);\n";
        $routesContent .= "Route::post('/register', [{$tableNameToUpper}Controller::class, 'register']);\n";

        file_put_contents($routePath, $routesContent);
        echo "✅ Routes créées : $routePath\n";
    }
}
