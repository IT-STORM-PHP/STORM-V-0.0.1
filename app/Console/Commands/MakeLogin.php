<?php

namespace App\Console\Commands;

class MakeLogin
{
    public function execute()
    {
        echo "Nom de la table utilisateur : ";
        $tableName = trim(fgets(STDIN));

        if (empty($tableName)) {
            echo "Erreur : Le nom de la table ne peut pas être vide.\n";
            return;
        }

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

            $columns[] = [
                'name' => $columnName,
                'type' => $columnType,
                'is_password' => $isPassword
            ];
        }

        // Générer la migration
        $this->generateMigration($tableName, $columns);

        // Générer le contrôleur LoginController
        $this->generateController();

        // Générer les routes uniquement si elles n'existent pas déjà
        $this->generateRoutes();

        echo "✅ Système de login généré avec succès !\n";
    }

    private function generateMigration($tableName, $columns)
    {
        $filename = "database/migrations/" .  "{$tableName}.php";
        $migrationContent = "<?php\n\n";
        $migrationContent .= "namespace Database\Migrations;\n\n";
        $migrationContent .= "use App\Schema\Blueprint;\n";
        $migrationContent .= "use App\Database\Migration;\n\n";
        $migrationContent .= "class " . ucfirst($tableName) . " extends Migration\n{\n";
        $migrationContent .= "    public function up()\n    {\n";
        $migrationContent .= "        // Création de la table avec le nom spécifié\n";
        $migrationContent .= "        \$table = new Blueprint('$tableName');\n";
        $migrationContent .= "        \$table->id('id');  // Création de la colonne ID (clé primaire)\n";

        foreach ($columns as $col) {
            $migrationContent .= "        // Ajout de la colonne {$col['name']} de type {$col['type']}\n";
            if ($col['is_password']) {
                $migrationContent .= "        \$table->string('{$col['name']}');  // Champ mot de passe\n";
            } else {
                $migrationContent .= "        \$table->{$col['type']}('{$col['name']}');\n";
            }
        }

        $migrationContent .= "        \$this->executeSQL(\$table->getSQL());\n";
        $migrationContent .= "    }\n\n";
        $migrationContent .= "    public function down()\n    {\n";
        $migrationContent .= "        \$table = new Blueprint('$tableName');\n";
        $migrationContent .= "        \$this->executeSQL(\$table->dropSQL());\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "}\n";

        file_put_contents($filename, $migrationContent);
        echo "✅ Migration créée : $filename\n";
    }

    private function generateController()
    {
        $controllerPath = "app/Controllers/LoginController.php";
        $controllerContent = "<?php\n\n";
        $controllerContent .= "namespace App\Controllers;\n\n";
        $controllerContent .= "use App\Core\Controller;\n";
        $controllerContent .= "use App\Models\User;\n";
        $controllerContent .= "use App\Core\Request;\n";
        $controllerContent .= "use App\Core\Session;\n\n";
        $controllerContent .= "class LoginController extends Controller\n{\n";
        $controllerContent .= "    public function showLoginForm()\n    {\n";
        $controllerContent .= "        return \$this->view('login');\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    public function login()\n    {\n";
        $controllerContent .= "        \$request = new Request();\n";
        $controllerContent .= "        \$data = \$request->getBody();\n\n";

        $controllerContent .= "        // Vérification de l'utilisateur\n";
        $controllerContent .= "        \$user = User::where(['email' => \$data['email']])->first();\n\n";
        $controllerContent .= "        if (!\$user || !password_verify(\$data['password'], \$user->password)) {\n";
        $controllerContent .= "            Session::setFlash('error', 'Identifiants incorrects.');\n";
        $controllerContent .= "            return redirect('/login');\n";
        $controllerContent .= "        }\n\n";
        $controllerContent .= "        Session::set('user', \$user->id);\n";
        $controllerContent .= "        return redirect('/dashboard');\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "}\n";

        file_put_contents($controllerPath, $controllerContent);
        echo "✅ Contrôleur créé : $controllerPath\n";
    }

    private function generateRoutes()
    {
        $routesPath = "routes/web.php";
        if (file_exists($routesPath) && strpos(file_get_contents($routesPath), 'LoginController') !== false) {
            echo "✅ Les routes sont déjà générées.\n";
            return;
        }

        $routesContent = "\n// Routes login\n";
        $routesContent .= "use App\Controllers\LoginController;\n";
        $routesContent .= "Route::get('/login', [LoginController::class, 'showLoginForm']);\n";
        $routesContent .= "Route::post('/login', [LoginController::class, 'login']);\n";
        file_put_contents($routesPath, $routesContent, FILE_APPEND);
        echo "✅ Routes ajoutées avec succès.\n";
    }
}
