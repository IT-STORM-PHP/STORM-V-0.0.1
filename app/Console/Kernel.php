<?php

namespace App\Console;

use PDO;
use App\Models\Database;
use App\Console\Commands\MakeLogin;
use App\Console\Commands\MakeModel;

class Kernel
{
    protected array $commands = [
        'serve' => 'serve',
        'make:migrations' => 'makeMigration',
        'migrate' => 'migrate',
        'migrateTest' => 'migrateTest',
        'rollback' => 'rollback',
        'make:crud' => 'makeCrud',
        'make:controllers' => 'makeController',
        'make:login' => 'makeLogin',
        'make:model' => 'makeModel',

    ];

    public function handle($argv)
    {
        $command = $argv[1] ?? null;
        $argument = $argv[2] ?? null;

        if (!$command || !isset($this->commands[$command])) {
            $this->showUsage();
            exit(1);
        }

        $method = $this->commands[$command];
        $this->$method($argument);
    }

    public function makeLogin()
    {
        $makeLogin = new MakeLogin();
        $makeLogin->run();
    }
    public function makeModel()
    {
        $makeModel = new MakeModel();
        $makeModel->execute();
    }

    protected function serve()
    {
        global $argv;

        $host = "127.0.0.1"; // Valeur par défaut
        $port = 8000;        // Valeur par défaut

        foreach ($argv as $arg) {
            if (strpos($arg, '--host=') === 0) {
                $host = substr($arg, 7);
            } elseif (strpos($arg, '--port=') === 0) {
                $port = (int) substr($arg, 7);
            }
        }

        while (!@stream_socket_server("tcp://$host:$port")) {
            $port++; // Incrémente si le port est occupé
        }

        $cmd = "php -S $host:$port -t public";
        echo "Serveur démarré sur http://$host:$port\n";
        exec($cmd);
    }

    protected function makeMigration($name)
    {
        if (!$name) {
            echo "❌ Veuillez fournir un nom de migration.\n";
            exit(1);
        }

        // Utilisation du nom directement sans datation
        $filename = "database/migrations/{$name}.php";
        $classname = ucfirst($name);

        // Contenu de la migration
        $content = "<?php\n\nnamespace Database\Migrations;\n\nuse App\Schema\Blueprint;\nuse App\Database\Migration;\n\nclass {$classname} extends Migration\n{\n";
        $content .= "    public function up()\n    {\n";
        $content .= "        \$table = new Blueprint('$name');\n";
        $content .= "        \$table->id();\n";
        $content .= "        \$table->string('name');\n";
        $content .= "        \$table->timestamps();\n";
        $content .= "        \$this->executeSQL(\$table->updateOrCreate());\n";
        $content .= "    }\n\n";
        $content .= "    public function down()\n    {\n";
        $content .= "        \$table = new Blueprint('$name');\n";
        $content .= "        \$this->executeSQL(\$table->dropSQL());\n";
        $content .= "    }\n}\n";

        // Vérifier si le dossier de migration existe
        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0777, true);
        }

        // Créer le fichier de migration et y écrire le contenu
        file_put_contents($filename, $content);
        echo "✅ Migration créée : $filename\n";
    }

    protected function migrate()
    {
        echo "🚀 Exécution des migrations...\n";

        // Vérifier si la table 'migrations' existe
        $this->checkMigrationsTable();

        // Récupérer tous les fichiers de migration dans le dossier 'migrations'
        $files = glob(__DIR__ . '/../../database/migrations/*.php');
        sort($files); // Trie les fichiers de migration

        // Récupérer les migrations déjà appliquées
        $appliedMigrations = $this->getAppliedMigrations();

        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);

            // Vérifier si la migration a déjà été appliquée
            if (in_array($migrationName, $appliedMigrations)) {
                echo "✅ Migration déjà appliquée : $migrationName\n";
                continue; // Passer à la suivante
            }

            require_once $file;

            // Extraire le nom de la classe
            $className = 'Database\\Migrations\\' . preg_replace('/^\d+_\d+_\d+_\d+_\d+_\d+_/', '', $migrationName);

            if (class_exists($className)) {
                $migration = new $className();
                try {
                    echo "🔧 Exécution de la migration : $className\n";
                    $migration->up();
                    $this->recordMigration($migrationName); // Enregistrer la migration dans la table
                    echo "✅ Migration réussie : $className\n";
                } catch (\Exception $e) {
                    echo "❌ Erreur lors de l'exécution de la migration : " . $e->getMessage() . "\n";
                }
            } else {
                echo "❌ Erreur : La classe '$className' n'existe pas dans le fichier '$file'.\n";
            }
        }

        echo "✅ Toutes les migrations exécutées.\n";
    }

    //Méthode pour lancer le migrate amélioré
    protected function migrateTest(){
        echo "🚀 Exécution des migrations...\n";

        // Récupérer tous les fichiers de migration dans le dossier 'migrations'
        $files = glob(__DIR__ . '/../../database/migrations/*.php');
        sort($files); // Trie les fichiers de migration
        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            require_once $file;

            // Extraire le nom de la classe
            $className = 'Database\\Migrations\\' . preg_replace('/^\d+_\d+_\d+_\d+_\d+_\d+_/', '', $migrationName);

            if (class_exists($className)) {
                $migration = new $className();
                try {
                    echo "🔧 Exécution de la migration : $className\n";
                    $migration->up();
                    
                    echo "✅ Migration réussie : $className\n";
                } catch (\Exception $e) {
                    echo "❌ Erreur lors de l'exécution de la migration : " . $e->getMessage() . "\n";
                }
            } else {
                echo "❌ Erreur : La classe '$className' n'existe pas dans le fichier '$file'.\n";
            }

        }

    }

    // Méthode pour vérifier si la table 'migrations' existe, sinon la créer
    private function checkMigrationsTable()
    {
        $pdo = \App\Models\Database::getInstance()->getConnection();

        // Vérifier si la table 'migrations' existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
        if ($stmt->rowCount() === 0) {
            // Si la table n'existe pas, on la crée
            $createTableSQL = "
            CREATE TABLE migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration_name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $pdo->exec($createTableSQL);
            echo "✅ Table 'migrations' créée.\n";
        }
    }

    private function getAppliedMigrations()
    {
        // Utiliser la classe Database pour obtenir la connexion
        $pdo = \App\Models\Database::getInstance()->getConnection();

        $query = "SELECT migration_name FROM migrations";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function recordMigration($migrationName)
    {
        // Utiliser la classe Database pour obtenir la connexion
        $pdo = \App\Models\Database::getInstance()->getConnection();

        // Enregistrer la migration dans la table 'migrations'
        $query = "INSERT INTO migrations (migration_name) VALUES (:migration_name)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['migration_name' => $migrationName]);
    }

    protected function rollback()
    {
        echo "⏪ Annulation des dernières migrations...\n";
        $files = glob(__DIR__ . '/../../database/migrations/*.php');
        rsort($files); // Exécute les rollbacks en ordre inverse

        foreach ($files as $file) {
            require_once $file;
            $className = 'Database\\Migrations\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className)) {
                $migration = new $className();
                echo "🔄 Rollback : " . $className . "\n";
                $migration->down();
            }
        }
        echo "✅ Rollback terminé.\n";
    }

    protected function makeCrud($model)
    {
        if (!$model) {
            echo "❌ Veuillez fournir un nom pour le modèle.\n";
            return;
        }

        // Mettre la première lettre en majuscule
        $model = ucfirst($model);

        // 1. Vérifier si la migration existe pour ce modèle
        $pdo = \App\Models\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM migrations WHERE migration_name = :model");
        $stmt->execute(['model' => strtolower($model)]); // Comparaison en minuscule

        if ($stmt->rowCount() === 0) {
            echo "❌ Aucune migration trouvée pour le modèle '$model'.\n";
            return;
        }

        // 2. Récupérer la structure de la table
        $stmt = $pdo->prepare("DESCRIBE " . strtolower($model));
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $modelLower = strtolower($model);
        // 3. Créer le dossier pour les vues
        $viewDir = __DIR__ . "../../Views/{$modelLower}";
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0777, true);
        }

        // Créer les vues pour la liste, la création, la modification et la suppression
        $this->createCrudViews($viewDir, $model, $columns);

        // 4. Ajouter les routes dans `routes/web.php`
        $this->addRoutesToWeb($model);

        $this->createController($model, $columns);

        // 3. Générer le modèle
        $modelContent = "<?php\n\nnamespace App\Models;\n\n";
        $modelContent .= "use PDO;\n";
        $modelContent .= "use App\Models\Model;\n\ndate_default_timezone_set('GMT');\n\n";
        $modelContent .= "class {$model} extends Model\n{\n";

        // Ajouter l'attribut privé pour PDO
        $modelContent .= "    private \$pdo;\n\n";

        // Constructeur
        $modelContent .= "    public function __construct()\n    {\n";
        $modelContent .= "        \$this->pdo = \App\Models\Database::getInstance()->getConnection();\n";
        $modelContent .= "    }\n";

        // Ajouter les attributs du modèle
        foreach ($columns as $column) {
            $modelContent .= "    public \${$column['Field']};\n";
        }

        // Méthode Create
        $modelContent .= "\n    public function create(\$data)\n    {\n";
        $modelContent .= "        \$sql = \"INSERT INTO " . strtolower($model) . " (" . implode(", ", array_column($columns, 'Field')) . ") VALUES (:" . implode(", :", array_column($columns, 'Field')) . ")\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        foreach ($columns as $column) {
            if ($column['Field'] == 'created_at' || $column['Field'] == 'updated_at') {
                $modelContent .= "       \$stmt->bindParam(':{$column['Field']}', date('Y-m-d H:i:s'));\n";
            }else{
                $modelContent .= "        \$stmt->bindParam(':{$column['Field']}', \$data['{$column['Field']}']);\n";
            }
        }
        $modelContent .= "        return \$stmt->execute();\n";
        $modelContent .= "    }\n";

        // Méthode Get All
        $modelContent .= "\n    public function getAll()\n    {\n";
        $modelContent .= "        \$sql = \"SELECT * FROM " . strtolower($model) . "\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->query(\$sql);\n";
        $modelContent .= "        return \$stmt->fetchAll();\n";
        $modelContent .= "    }\n";

        // Méthode Read (find by id)
        $modelContent .= "\n    public function read(\$id)\n    {\n";
        $modelContent .= "        \$sql = \"SELECT * FROM " . strtolower($model) . " WHERE id = :id\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        $modelContent .= "        \$stmt->bindParam(':id', \$id);\n";
        $modelContent .= "        \$stmt->execute();\n";
        $modelContent .= "        return \$stmt->fetch(PDO::FETCH_ASSOC);\n";
        $modelContent .= "    }\n";

        // Méthode Update
        $modelContent .= "\n    public function update(\$id, \$data)\n    {\n";
        $modelContent .= "        \$sql = \"UPDATE " . strtolower($model) . " SET ";
        // Filtrer les colonnes pour exclure 'created_at'
        $filteredColumns = array_filter($columns, fn($col) => $col['Field'] !== 'created_at');
        $modelContent .= implode(", ", array_map(fn($col) => "{$col['Field']} = :{$col['Field']}", $filteredColumns));
        $modelContent .= " WHERE id = :id\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        
        // Lier uniquement les colonnes qui ne sont pas 'created_at'
        foreach ($filteredColumns as $column) {
            $modelContent .= "        \$stmt->bindParam(':{$column['Field']}', \$data['{$column['Field']}']);\n";
        }
        
        $modelContent .= "        \$stmt->bindParam(':id', \$id);\n";
        $modelContent .= "        return \$stmt->execute();\n";
        $modelContent .= "    }\n";
        
        


        // Méthode Delete
        $modelContent .= "\n    public function delete(\$id)\n    {\n";
        $modelContent .= "        \$sql = \"DELETE FROM " . strtolower($model) . " WHERE id = :id\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        $modelContent .= "        \$stmt->bindParam(':id', \$id);\n";
        $modelContent .= "        return \$stmt->execute();\n";
        $modelContent .= "    }\n";

        // Fermer la classe
        $modelContent .= "}\n";

        // Créer le fichier du modèle
        file_put_contents("app/Models/{$model}.php", $modelContent);
        echo "✅ Modèle '$model' avec méthodes CRUD créé.\n";
    }

    protected function createCrudViews($viewDir, $model, $columns)
    {
        $modelLower = strtolower($model);

        function getInputType($type)
        {
            if (str_contains($type, 'int') || str_contains($type, 'float') || str_contains($type, 'double') || str_contains($type, 'decimal')) {
                return 'number';
            } elseif (str_contains($type, 'varchar') || str_contains($type, 'text')) {
                return 'text';
            } elseif (str_contains($type, 'date')) {
                return 'date';
            } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
                return 'datetime-local';
            } elseif (str_contains($type, 'boolean') || str_contains($type, 'tinyint(1)')) {
                return 'select';
            }
            return 'text';
        }

        /* function readTemplate($path, $model = null)
        {
            $content = file_get_contents(__DIR__ . "../../../public/{$path}.php");

            
            if ($model !== null) {
                $content = str_replace('{$model}', $model, $content);
            }

            return $content;
        }


        $htmlHeader = readTemplate('_header', $model);
        $htmlFooter = readTemplate('_footer', $model); */

        // Vue Index
        $listViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<h1 class='mb-4'>{$model} List</h1>\n<a href='/{$modelLower}/create' class='btn btn-primary mb-3'>Create {$model}</a>\n<table class='table'>\n<thead class='table-light'><tr>";

        foreach ($columns as $column) {
            $listViewContent .= "<th>{$column['Field']}</th>";
        }
        $listViewContent .= "<th>Actions</th></tr></thead><tbody>\n<?php foreach (\$items as \$item): ?>\n<tr>";
        foreach ($columns as $column) {
            $listViewContent .= "<td><?php echo htmlspecialchars(\$item['{$column['Field']}']); ?></td>";
        }
        $listViewContent .= "<td>
        <a href='/{$modelLower}/show/<?php echo \$item['id']; ?>' class='btn btn-info btn-sm'>Show</a>
        <a href='/{$modelLower}/edit/<?php echo \$item['id']; ?>' class='btn btn-warning btn-sm'>Edit</a>
        <form action='/{$modelLower}/delete/<?php echo \$item['id']; ?>' method='POST' class='d-inline'>
            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
        </form>
        </td></tr>\n<?php endforeach; ?>\n</tbody></table>\n<?php \$content = ob_get_clean();?>\n";
        file_put_contents("{$viewDir}/index.php", $listViewContent);

        // Vue Create
        $createViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<h1>Create {$model}</h1>\n<form method='POST' action='/{$modelLower}/store' class='mt-4'>\n";
        foreach ($columns as $column) {
            if (in_array($column['Field'], ['id', 'created_at', 'updated_at'])) continue;
            $inputType = getInputType($column['Type']);
            $createViewContent .= "<div class='mb-3'><label class='form-label'>{$column['Field']}</label>";
            if ($inputType === 'select') {
                $createViewContent .= "<select name='{$column['Field']}' class='form-select'><option value='1'>Yes</option><option value='0'>No</option></select>";
            } else {
                $createViewContent .= "<input type='{$inputType}' name='{$column['Field']}' class='form-control'>";
            }
            $createViewContent .= "</div>";
        }
        $createViewContent .= "<button type='submit' class='btn btn-success'>Create {$model}</button>\n</form>\n<?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/create.php", $createViewContent);

        // Vue Show
        $showViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<h1 class='mb-4'>Show {$model}</h1>\n";
        $showViewContent .= "<?php if (!empty(\$item)): ?>\n";
        $showViewContent .= "<div class='card mb-4'>\n<div class='card-body'>\n";

        foreach ($columns as $column) {
            $showViewContent .= "<p><strong>{$column['Field']}:</strong> <?php echo htmlspecialchars(\$item['{$column['Field']}'] ?? 'N/A'); ?></p>\n";
        }

        $showViewContent .= "</div>\n</div>\n";
        $showViewContent .= "<?php else: ?>\n<p class='text-danger'>Aucune donnée trouvée.</p>\n<?php endif; ?>\n";
        $showViewContent .= "<a href='/{$modelLower}' class='btn btn-secondary'>Back to List</a>\n\n\<?php \$content = ob_get_clean();?>";

        file_put_contents("{$viewDir}/show.php", $showViewContent);

        // Vue Edit
        $editViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<h1>Edit {$model}</h1>\n<form method='POST' action='/{$modelLower}/update/<?php echo \$item['id']; ?>' class='mt-4'>\n";
        foreach ($columns as $column) {
            if (in_array($column['Field'], ['id', 'created_at', 'updated_at'])) continue;
            $inputType = getInputType($column['Type']);
            $editViewContent .= "<div class='mb-3'><label class='form-label'>{$column['Field']}</label>";
            if ($inputType === 'select') {
                $editViewContent .= "<select name='{$column['Field']}' class='form-select'>
                <option value='1' <?php echo \$item['{$column['Field']}'] == 1 ? 'selected' : ''; ?>>Yes</option>
                <option value='0' <?php echo \$item['{$column['Field']}'] == 0 ? 'selected' : ''; ?>>No</option>
            </select>";
            } else {
                $editViewContent .= "<input type='{$inputType}' name='{$column['Field']}' value='<?php echo htmlspecialchars(\$item['{$column['Field']}']); ?>' class='form-control'>";
            }
            $editViewContent .= "</div>";
        }
        $editViewContent .= "<button type='submit' class='btn btn-primary'>Update {$model}</button>\n</form>\n<?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/edit.php", $editViewContent);

        // Vue Delete
        $deleteViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<h1 class='text-danger'>Are you sure you want to delete this {$model}?</h1>\n<form method='POST' action='/{$modelLower}/delete/<?php echo \$item['id']; ?>'>\n<button type='submit' class='btn btn-danger'>Yes, Delete</button>\n<a href='/{$modelLower}' class='btn btn-secondary'>Cancel</a>\n</form>\n<?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/delete.php", $deleteViewContent);
    }




    protected function addRoutesToWeb($model)
    {
        $webPath = __DIR__ . '/../../routes/web.php';
        $controllerClass = ucfirst($model) . 'Controller';
        $namespaceLine = "use App\Controllers\\$controllerClass;";

        // Convertir $model en minuscule pour l'URL
        $modelLower = strtolower($model);


        // Définition des routes pour le CRUD
        $routes = [
            "Route::get('/{$modelLower}', [{$controllerClass}::class, 'index']);",
            "Route::get('/{$modelLower}/create', [{$controllerClass}::class, 'create']);",
            "Route::get('/{$modelLower}/show/{id}', [{$controllerClass}::class, 'show']);",
            "Route::post('/{$modelLower}/store', [{$controllerClass}::class, 'store']);",
            "Route::get('/{$modelLower}/edit/{id}', [{$controllerClass}::class, 'edit']);",
            "Route::post('/{$modelLower}/update/{id}', [{$controllerClass}::class, 'update']);",
            "Route::post('/{$modelLower}/delete/{id}', [{$controllerClass}::class, 'destroy']);",
        ];

        // Lire le contenu actuel du fichier
        $existingRoutes = file_get_contents($webPath);

        // Vérifier si l'import du contrôleur existe déjà
        if (!str_contains($existingRoutes, $namespaceLine)) {
            file_put_contents($webPath, $namespaceLine . "\n", FILE_APPEND);
        }

        // Vérifier si les routes existent déjà pour éviter les doublons
        $newRoutes = [];
        foreach ($routes as $route) {
            if (!str_contains($existingRoutes, $route)) {
                $newRoutes[] = $route;
            }
        }

        // Ajouter les nouvelles routes si elles n'existent pas déjà
        if (!empty($newRoutes)) {
            file_put_contents($webPath, implode("\n", $newRoutes) . "\n", FILE_APPEND);
            echo "✅ Routes et namespace pour '$controllerClass' ajoutés à 'routes/web.php'.\n";
        } else {
            echo "⚠️ Les routes pour '$controllerClass' existent déjà dans 'routes/web.php'.\n";
        }
    }


    protected function createController($model, $columns)
    {
        // Créer le contrôleur
        $modelLower = strtolower($model);
        $controllerContent = "<?php\n\nnamespace App\Controllers;\n\n";
        $controllerContent .= "use App\Models\\{$model};\n";
        $controllerContent .= "use App\Views\View;\n";
        $controllerContent .= "use App\Http\Request;\n";
        $controllerContent .= "class {$model}Controller extends Controller\n{\n";
        $controllerContent .= "    private \$model, \$request;\n";
        $controllerContent .= "    public function __construct()\n    {\n";
        $controllerContent .= "        \$this->model = new {$model}();\n";
        $controllerContent .= "        \$this->request = new Request();\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    public function index()\n    {\n";
        $controllerContent .= "        // Logique pour afficher la liste\n";
        $controllerContent .= "        \$items = \$this->model->getAll();\n";
        $controllerContent .= "        return View::render('{$modelLower}/index', ['items' => \$items]);\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "    public function show(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour afficher un élément\n";
        $controllerContent .= "        \$item = \$this->model->read(\$id);\n";
        $controllerContent .= "        return View::render('{$modelLower}/show', ['item' => \$item]);\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "    public function create()\n    {\n";
        $controllerContent .= "        // Logique pour afficher le formulaire de création\n";
        $controllerContent .= "        return View::render('{$modelLower}/create');\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "    public function store()\n    {\n";
        $controllerContent .= "        // Logique pour enregistrer l'élément\n";
        $controllerContent .= "        \$data = [\n";
        foreach ($columns as $column) {
            $controllerContent .= "            '{$column['Field']}' => \$this->request->get('{$column['Field']}'),\n";
        }
        $controllerContent .= "        ];\n";
        $controllerContent .= "        \$this->model->create(\$data);\n";
        $controllerContent .= "        return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "    public function edit(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour afficher le formulaire de modification\n";
        $controllerContent .= "       \$item = \$this->model->read(\$id);\n";
        $controllerContent .= "       return View::render('{$modelLower}/edit', ['item' => \$item]);\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "    public function update(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour mettre à jour l'élément\n";
        $controllerContent .= "        \$data = [\n";
        foreach ($columns as $column) {
            $controllerContent .= "            '{$column['Field']}' => \$this->request->get('{$column['Field']}'),\n";
        }
        $controllerContent .= "        ];\n";
        $controllerContent .= "        \$this->model->update(\$id, \$data);\n";
        $controllerContent .= "        return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "    public function destroy(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour supprimer l'élément\n";
        $controllerContent .= "        \$this->model->delete(\$id);\n";
        $controllerContent .= "        return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "}\n";
        file_put_contents("app/Controllers/{$model}Controller.php", $controllerContent);
        echo "✅ Contrôleur '$model' créé.\n";
    }

    protected function makeController($controllerName)
    {
        if (!$controllerName) {
            echo "❌ Veuillez fournir un nom pour le contrôleur.\n";
            exit(1);
        }

        // Mettre la première lettre en majuscule
        $controllerName = ucfirst($controllerName);

        // Chemin du fichier
        $filePath = "app/Controllers/{$controllerName}.php";

        // Vérifier si le contrôleur existe déjà
        if (file_exists($filePath)) {
            echo "❌ Le contrôleur '$controllerName' existe déjà.\n";
            exit(1);
        }

        // Contenu du contrôleur
        $content = "<?php\n\nnamespace App\Controllers;\n\n";
        $content .= "use App\Controller\Controllers;\n\n";
        $content .= "class {$controllerName} extends Controller\n{\n";
        $content .= "    public function index()\n    {\n";
        $content .= "        // Action par défaut\n";
        $content .= "        echo 'Hello from {$controllerName} Controller';\n";
        $content .= "    }\n";
        $content .= "}\n";

        // Créer le fichier du contrôleur
        file_put_contents($filePath, $content);

        echo "✅ Contrôleur '$controllerName' créé dans 'app/Controllers'.\n";
    }



    protected function showUsage()
    {
        echo "Usage: php storm <commande>\n";
        echo "Commandes disponibles :\n";
        echo "  serve             Démarrer le serveur local\n";
        echo "  make:migrations   Créer un fichier de migration\n";
        echo "  migrate           Exécuter les migrations\n";
        echo "  migrateTest       Exécuter les migrations avec possibilité de mise à jour des tables (test) \n";
        echo "  rollback          Annuler la dernière migration\n";
        echo '  make:crud         Créer un modèle et un contrôleur CRUD pour une table existante' . "\n";
        echo '  make:controllers  Créer un contrôleur' . "\n";
        echo '  make:login        Créer un système de connexion avec une table existante' . "\n";
        echo '  make:model        Créer un modèle' . "\n";
    }
}
