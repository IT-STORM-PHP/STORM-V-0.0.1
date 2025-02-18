<?php

namespace App\Console\Commands;

class Migrate
{
    public function migrate()
    {
        echo "🚀 Exécution des migrations...\n";

        // Récupérer tous les fichiers de migration dans le dossier 'migrations'
        $files = glob(__DIR__ . '/../../../database/migrations/*.php');
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
}
