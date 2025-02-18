<?php

namespace App\Console\Commands\Crud;

class AddRoutesWeb
{
    public function addRoutesToWeb($model, $primaryKey = 'id')
    {
        $webPath = __DIR__ . '/../../../../routes/web.php';
        $controllerClass = ucfirst($model) . 'Controller';
        $namespaceLine = "\n\nuse App\Controllers\\$controllerClass;";

        // Convertir $model en minuscule pour l'URL
        $modelLower = strtolower($model);

        // Définition des routes pour le CRUD
        $routes = [
            "Route::get('/{$modelLower}', [{$controllerClass}::class, 'index']);",
            "Route::get('/{$modelLower}/create', [{$controllerClass}::class, 'create']);",
            "Route::get('/{$modelLower}/show/{{$primaryKey}}', [{$controllerClass}::class, 'show']);",
            "Route::post('/{$modelLower}/store', [{$controllerClass}::class, 'store']);",
            "Route::get('/{$modelLower}/edit/{{$primaryKey}}', [{$controllerClass}::class, 'edit']);",
            "Route::post('/{$modelLower}/update/{{$primaryKey}}', [{$controllerClass}::class, 'update']);",
            "Route::post('/{$modelLower}/delete/{{$primaryKey}}', [{$controllerClass}::class, 'destroy']);\n\n",
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
}
