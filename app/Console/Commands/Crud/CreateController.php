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
        $controllerContent .= "use App\Models\Database;\n"; // Ajout de l'utilisation de Database
        $controllerContent .= "class {$model}Controller extends Controller\n{\n";
        $controllerContent .= "    private \$model, \$request, \$pdo, \$foreignKeys;\n"; // Ajout de \$foreignKeys
        $controllerContent .= "    public function __construct()\n    {\n";
        $controllerContent .= "        \$this->model = new {$model}();\n";
        $controllerContent .= "        \$this->request = new Request();\n";
        $controllerContent .= "        \$this->pdo = Database::getInstance()->getConnection();\n"; // Initialisation de \$pdo
        $controllerContent .= "        \$this->foreignKeys = " . var_export($foreignKeys, true) . ";\n"; // Initialisation de \$foreignKeys
        $controllerContent .= "    }\n\n";

        // Méthode Index
        $controllerContent .= "    public function index()\n    {\n";
        $controllerContent .= "        // Logique pour afficher la liste\n";
        $controllerContent .= "        \$items = \$this->model->getAll();\n";
        $controllerContent .= "        // Joindre les données des tables référencées\n";
        $controllerContent .= "        foreach (\$items as &\$item) {\n";
        $controllerContent .= "            foreach (\$this->foreignKeys as \$column => \$foreignKey) {\n"; // Utilisation de \$this->foreignKeys
        $controllerContent .= "                \$foreignTable = \$foreignKey['table'];\n";
        $controllerContent .= "                \$foreignColumn = \$foreignKey['column'];\n";
        $controllerContent .= "                \$stmt = \$this->pdo->prepare(\"SELECT * FROM \" . \$foreignTable . \" WHERE \" . \$foreignColumn . \" = ?\");\n";
        $controllerContent .= "                \$stmt->execute([\$item[\$column]]);\n";
        $controllerContent .= "                \$foreignItem = \$stmt->fetch(\\PDO::FETCH_ASSOC);\n";
        $controllerContent .= "                \$item[\$foreignTable . '_' . \$foreignColumn] = \$foreignItem['name'] ?? 'N/A';\n"; // Afficher une valeur significative
        $controllerContent .= "            }\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "        return View::render('{$modelLower}/index', ['items' => \$items]);\n";
        $controllerContent .= "    }\n";

        // Méthode Show
        $controllerContent .= "    public function show(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour afficher un élément\n";
        $controllerContent .= "        \$item = \$this->model->read(\$id);\n";
        $controllerContent .= "        // Joindre les données des tables référencées\n";
        $controllerContent .= "        foreach (\$this->foreignKeys as \$column => \$foreignKey) {\n"; // Utilisation de \$this->foreignKeys
        $controllerContent .= "            \$foreignTable = \$foreignKey['table'];\n";
        $controllerContent .= "            \$foreignColumn = \$foreignKey['column'];\n";
        $controllerContent .= "            \$stmt = \$this->pdo->prepare(\"SELECT * FROM \" . \$foreignTable . \" WHERE \" . \$foreignColumn . \" = ?\");\n";
        $controllerContent .= "            \$stmt->execute([\$item[\$column]]);\n";
        $controllerContent .= "            \$foreignItem = \$stmt->fetch(\\PDO::FETCH_ASSOC);\n";
        $controllerContent .= "            \$item[\$foreignTable . '_' . \$foreignColumn] = \$foreignItem['name'] ?? 'N/A';\n"; // Afficher une valeur significative
        $controllerContent .= "        }\n";
        $controllerContent .= "        return View::render('{$modelLower}/show', ['item' => \$item]);\n";
        $controllerContent .= "    }\n";

        // Méthode Create
        $controllerContent .= "    public function create()\n    {\n";
        $controllerContent .= "        // Logique pour afficher le formulaire de création\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $foreignTable = $foreignKey['table'];
                $controllerContent .= "        \$stmt = \$this->pdo->query(\"SELECT * FROM {$foreignTable}\");\n";
                $controllerContent .= "        \${$foreignTable} = \$stmt->fetchAll(\\PDO::FETCH_ASSOC);\n";
            }
        }
        $controllerContent .= "        return View::render('{$modelLower}/create', [\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $controllerContent .= "            '{$foreignKey['table']}' => \${$foreignKey['table']},\n";
            }
        }
        $controllerContent .= "        ]);\n";
        $controllerContent .= "    }\n";

        // Méthode Store
        $controllerContent .= "    public function store()\n    {\n";
        $controllerContent .= "        // Logique pour enregistrer l'élément\n";
        $controllerContent .= "        \$data = [\n";
        foreach ($columns as $column) {
            if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at') {
                continue; // Ignorer les colonnes de timestamp
            }
            $controllerContent .= "            '{$column['Field']}' => \$this->request->get('{$column['Field']}'),\n";
        }
        $controllerContent .= "        ];\n";
        $controllerContent .= "        \$this->model->create(\$data);\n";
        $controllerContent .= "        return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "    }\n";

        // Méthode Edit
        $controllerContent .= "    public function edit(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour afficher le formulaire de modification\n";
        $controllerContent .= "        \$item = \$this->model->read(\$id);\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $foreignTable = $foreignKey['table'];
                $controllerContent .= "        \$stmt = \$this->pdo->query(\"SELECT * FROM {$foreignTable}\");\n";
                $controllerContent .= "        \${$foreignTable} = \$stmt->fetchAll(\\PDO::FETCH_ASSOC);\n";
            }
        }
        $controllerContent .= "        return View::render('{$modelLower}/edit', [\n";
        $controllerContent .= "            'item' => \$item,\n";
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $column => $foreignKey) {
                $controllerContent .= "            '{$foreignKey['table']}' => \${$foreignKey['table']},\n";
            }
        }
        $controllerContent .= "        ]);\n";
        $controllerContent .= "    }\n";

        // Méthode Update
        $controllerContent .= "    public function update(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour mettre à jour l'élément\n";
        $controllerContent .= "        \$data = [\n";
        foreach ($columns as $column) {
            if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at') {
                continue; // Ignorer les colonnes de timestamp
            }
            $controllerContent .= "            '{$column['Field']}' => \$this->request->get('{$column['Field']}'),\n";
        }
        $controllerContent .= "        ];\n";
        $controllerContent .= "        \$this->model->update(\$id, \$data);\n";
        $controllerContent .= "        return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "    }\n";

        // Méthode Destroy
        $controllerContent .= "    public function destroy(\$id)\n    {\n";
        $controllerContent .= "        // Logique pour supprimer l'élément\n";
        $controllerContent .= "        \$this->model->delete(\$id);\n";
        $controllerContent .= "        return View::redirect('/{$modelLower}');\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "}\n";

        // Créer le fichier du contrôleur
        file_put_contents("app/Controllers/{$model}Controller.php", $controllerContent);
        echo "✅ Contrôleur '$model' créé.\n";
    }
}