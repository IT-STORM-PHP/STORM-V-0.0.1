<?php

namespace App\Console\Commands\Crud;

class CreateController
{
    public function createController($model, $columns)
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
}
