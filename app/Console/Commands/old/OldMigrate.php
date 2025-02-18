<?php
    namespace App\Console\Commands\Old;

    
    use PDO;
    class OldMigrate{

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


    }
