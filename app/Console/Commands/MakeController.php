<?php

namespace App\Console\Commands;

class MakeController
{
    public function makeController($controllerName)
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
}
