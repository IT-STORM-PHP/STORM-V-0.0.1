<?php

namespace App\Console\Commands\Crud;

class CreateController
{
    public function createController($model, $columns, $foreignKeys = [])
    {
        // Créer le contrôleur
        $modelLower = strtolower($model);
        $controllerContent = "<?php\n\nnamespace App\Controllers;\n\n";
        $controllerContent .= "use App\Models\\{$model};\n";
        $controllerContent .= "use App\Views\View;\n";
        $controllerContent .= "use App\Http\Request;\n";
        $controllerContent .= "use App\Models\Database;\n";
        $controllerContent .= "use PDO;\n\n";
        $controllerContent .= "class {$model}Controller extends Controller\n{\n";
        $controllerContent .= "    private \$model, \$request, \$pdo, \$foreignKeys;\n";
        $controllerContent .= "    public function __construct()\n    {\n";
        $controllerContent .= "        \$this->model = new {$model}();\n";
        $controllerContent .= "        \$this->request = new Request();\n";
        $controllerContent .= "        \$this->pdo = Database::getInstance()->getConnection();\n";
        $controllerContent .= "        \$this->foreignKeys = " . var_export($foreignKeys, true) . ";\n";
        $controllerContent .= "    }\n\n";

        // Méthode Index
        $controllerContent .= "    public function index()\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            // Récupérer tous les éléments\n";
        $controllerContent .= "            \$items = \$this->model->getAll();\n\n";
        
        $controllerContent .= "            // Récupérer les données associées pour toutes les clés étrangères\n";
        $controllerContent .= "            foreach (\$this->foreignKeys as \$column => \$foreignKey) {\n";
        $controllerContent .= "                \$foreignTable = \$foreignKey['table'];\n";
        $controllerContent .= "                \$foreignColumn = \$foreignKey['column'];\n";
        $controllerContent .= "                \$displayColumn = \$foreignKey['display_column'] ?? \$foreignColumn; // Utiliser la colonne significative\n\n";
        
        $controllerContent .= "                // Récupérer toutes les données de la table étrangère\n";
        $controllerContent .= "                \$stmt = \$this->pdo->query(\"SELECT * FROM \" . \$foreignTable);\n";
        $controllerContent .= "                \$foreignData = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n\n";
        
        $controllerContent .= "                // Associer les données étrangères aux éléments\n";
        $controllerContent .= "                foreach (\$items as &\$item) {\n";
        $controllerContent .= "                    foreach (\$foreignData as \$foreignItem) {\n";
        $controllerContent .= "                        if (\$item[\$column] == \$foreignItem[\$foreignColumn]) {\n";
        $controllerContent .= "                            \$item[\$foreignKey['table'] . '_' . \$displayColumn] = \$foreignItem[\$displayColumn] ?? 'N/A';\n"; // Utiliser la colonne significative
        $controllerContent .= "                            break;\n";
        $controllerContent .= "                        }\n";
        $controllerContent .= "                    }\n";
        $controllerContent .= "                }\n";
        $controllerContent .= "            }\n\n";
        
        $controllerContent .= "            return View::render('{$modelLower}/index', ['items' => \$items]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";

        $controllerContent .= "    public function show(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            // Récupérer l'élément spécifique\n";
        $controllerContent .= "            \$item = \$this->model->read(\$id);\n";
        $controllerContent .= "            if (!\$item) {\n";
        $controllerContent .= "                return View::render('error', ['message' => '{$model} not found']);\n";
        $controllerContent .= "            }\n\n";
        
        $controllerContent .= "            // Récupérer les données associées pour toutes les clés étrangères\n";
        $controllerContent .= "            foreach (\$this->foreignKeys as \$column => \$foreignKey) {\n";
        $controllerContent .= "                \$foreignTable = \$foreignKey['table'];\n";
        $controllerContent .= "                \$foreignColumn = \$foreignKey['column'];\n";
        $controllerContent .= "                \$displayColumn = \$foreignKey['display_column'] ?? \$foreignColumn; // Utiliser la colonne significative\n\n";
        
        $controllerContent .= "                // Récupérer les données de la table étrangère\n";
        $controllerContent .= "                \$stmt = \$this->pdo->prepare(\"SELECT * FROM \" . \$foreignTable . \" WHERE \" . \$foreignColumn . \" = ?\");\n";
        $controllerContent .= "                \$stmt->execute([\$item[\$column]]);\n";
        $controllerContent .= "                \$foreignItem = \$stmt->fetch(PDO::FETCH_ASSOC);\n\n";
        
        $controllerContent .= "                // Associer les données étrangères à l'élément\n";
        $controllerContent .= "                \$item[\$foreignKey['table'] . '_' . \$displayColumn] = \$foreignItem[\$displayColumn] ?? 'N/A';\n"; // Utiliser la colonne significative
        $controllerContent .= "            }\n\n";
        
        $controllerContent .= "            return View::render('{$modelLower}/show', ['item' => \$item]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";
        // Méthode Create
        $controllerContent .= "    public function create()\n    {\n";
        $controllerContent .= "        try {\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $foreignTable = $foreignKey['table'];
                $controllerContent .= "            \$stmt = \$this->pdo->query(\"SELECT * FROM {$foreignTable}\");\n";
                $controllerContent .= "            \${$foreignTable} = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n";
            }
        }
        $controllerContent .= "            return View::render('{$modelLower}/create', [\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $controllerContent .= "                '{$foreignKey['table']}' => \${$foreignKey['table']},\n";
            }
        }
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";

        // Méthode Store
        $controllerContent .= "    public function store()\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$data = [\n";
        foreach ($columns as $column) {
            if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at') {
                continue;
            }
            $controllerContent .= "                '{$column['Field']}' => \$this->request->get('{$column['Field']}'),\n";
        }
        $controllerContent .= "            ];\n";
        $controllerContent .= "            \$this->model->create(\$data);\n";
        $controllerContent .= "            return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";

        // Méthode Edit
        $controllerContent .= "    public function edit(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$item = \$this->model->read(\$id);\n";
        $controllerContent .= "            if (!\$item) {\n";
        $controllerContent .= "                return View::render('error', ['message' => '{$model} not found']);\n";
        $controllerContent .= "            }\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $foreignTable = $foreignKey['table'];
                $controllerContent .= "            \$stmt = \$this->pdo->query(\"SELECT * FROM {$foreignTable}\");\n";
                $controllerContent .= "            \${$foreignTable} = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n";
            }
        }
        $controllerContent .= "            return View::render('{$modelLower}/edit', [\n";
        $controllerContent .= "                'item' => \$item,\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $controllerContent .= "                '{$foreignKey['table']}' => \${$foreignKey['table']},\n";
            }
        }
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";

        // Méthode Update
        $controllerContent .= "    public function update(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$data = [\n";
        foreach ($columns as $column) {
            if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at') {
                continue;
            }
            $controllerContent .= "                '{$column['Field']}' => \$this->request->get('{$column['Field']}'),\n";
        }
        $controllerContent .= "            ];\n";
        $controllerContent .= "            \$this->model->update(\$id, \$data);\n";
        $controllerContent .= "            return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";

        // Méthode Destroy
        $controllerContent .= "    public function destroy(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$this->model->delete(\$id);\n";
        $controllerContent .= "            return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return View::render('error', ['message' => \$e->getMessage()]);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "}\n";

        // Créer le fichier du contrôleur
        file_put_contents("app/Controllers/{$model}Controller.php", $controllerContent);
        echo "✅ Contrôleur '$model' créé.\n";
    }
}