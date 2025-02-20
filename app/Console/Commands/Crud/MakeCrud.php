<?php

namespace App\Console\Commands\Crud;

use PDO;
use App\Console\Commands\Crud\CreateCrudViews;
use App\Console\Commands\Crud\AddRoutesWeb;
use App\Console\Commands\Crud\CreateController;

class MakeCrud
{
    private $createCrudViews, $addRoutesToWeb, $createController;

    public function __construct()
    {
        $this->createCrudViews = new CreateCrudViews();
        $this->addRoutesToWeb = new AddRoutesWeb();
        $this->createController = new CreateController();
    }

    public function makeCrud($model)
    {
        if (!$model) {
            echo "❌ Veuillez fournir un nom pour le modèle.\n";
            return;
        }

        // Mettre la première lettre en majuscule
        $model = ucfirst($model);
        $modelLower = strtolower($model);

        // Connexion à la base de données
        $pdo = \App\Models\Database::getInstance()->getConnection();

        // Vérifier si la table existe dans la base de données
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table");
        $stmt->execute(['table' => $modelLower]);
        $tableExists = $stmt->fetchColumn();

        if (!$tableExists) {
            echo "❌ La table '$modelLower' n'existe pas dans la base de données.\n";
            return;
        }

        // 1. Récupérer la structure de la table
        $stmt = $pdo->prepare("DESCRIBE " . $modelLower);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Récupérer les clés étrangères
        $foreignKeys = $this->getForeignKeys($pdo, $modelLower);

        // 3. Créer le dossier pour les vues
        $viewDir = __DIR__ . "/../../../Views/{$modelLower}";
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0777, true);
        }

        // Créer les vues pour la liste, la création, la modification et la suppression
        $this->createCrudViews->createCrudViews($viewDir, $model, $columns, $foreignKeys);

        // 4. Ajouter les routes dans `routes/web.php`
        $this->addRoutesToWeb->addRoutesToWeb($model);

        // 5. Générer le contrôleur
        $this->createController->createController($model, $columns, $foreignKeys);

        // 6. Générer le modèle
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
        $modelContent .= "        \$sql = \"INSERT INTO " . $modelLower . " (" . implode(", ", array_column($columns, 'Field')) . ") VALUES (:" . implode(", :", array_column($columns, 'Field')) . ")\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        foreach ($columns as $column) {
            if ($column['Field'] == 'created_at' || $column['Field'] == 'updated_at') {
                $modelContent .= "       \$stmt->bindValue(':{$column['Field']}', date('Y-m-d H:i:s'));\n";
            } else {
                $modelContent .= "        \$stmt->bindValue(':{$column['Field']}', \$data['{$column['Field']}']);\n";
            }
        }
        $modelContent .= "        return \$stmt->execute();\n";
        $modelContent .= "    }\n";

        // Méthode Get All
        $modelContent .= "\n    public function getAll()\n    {\n";
        $modelContent .= "        \$sql = \"SELECT * FROM " . $modelLower . "\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->query(\$sql);\n";
        $modelContent .= "        return \$stmt->fetchAll();\n";
        $modelContent .= "    }\n";

        // Trouver la clé primaire dynamiquement
        $primaryKey = 'id';
        foreach ($columns as $column) {
            if ($column['Key'] === 'PRI') {
                $primaryKey = $column['Field'];
                break;
            }
        }

        // Méthode Read (find by id)
        $modelContent .= "\n    public function read(\$id)\n    {\n";
        $modelContent .= "        \$sql = \"SELECT * FROM " . $modelLower . " WHERE {$primaryKey} = :{$primaryKey}\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        $modelContent .= "        \$stmt->bindParam(':{$primaryKey}', \$id);\n";
        $modelContent .= "        \$stmt->execute();\n";
        $modelContent .= "        return \$stmt->fetch(PDO::FETCH_ASSOC);\n";
        $modelContent .= "    }\n";

        // Méthode Update
        $modelContent .= "\n    public function update(\$id, \$data)\n    {\n";
        $modelContent .= "        \$sql = \"UPDATE " . $modelLower . " SET ";
        $filteredColumns = array_filter($columns, fn($col) => $col['Field'] !== 'created_at' && $col['Field'] !== $primaryKey);
        $modelContent .= implode(", ", array_map(fn($col) => "{$col['Field']} = :{$col['Field']}", $filteredColumns));
        $modelContent .= " WHERE {$primaryKey} = :{$primaryKey}\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";

        // Lier uniquement les colonnes qui ne sont pas 'created_at' ni la clé primaire
        foreach ($filteredColumns as $column) {
            $modelContent .= "        \$stmt->bindValue(':{$column['Field']}', \$data['{$column['Field']}']);\n";
        }

        $modelContent .= "        \$stmt->bindParam(':{$primaryKey}', \$id);\n";
        $modelContent .= "        return \$stmt->execute();\n";
        $modelContent .= "    }\n";

        // Méthode Delete
        $modelContent .= "\n    public function delete(\$id)\n    {\n";
        $modelContent .= "        \$sql = \"DELETE FROM " . $modelLower . " WHERE {$primaryKey} = :{$primaryKey}\";\n";
        $modelContent .= "        \$stmt = \$this->pdo->prepare(\$sql);\n";
        $modelContent .= "        \$stmt->bindParam(':{$primaryKey}', \$id);\n";
        $modelContent .= "        return \$stmt->execute();\n";
        $modelContent .= "    }\n";

        // Fermer la classe
        $modelContent .= "}\n";

        // Créer le fichier du modèle
        file_put_contents("app/Models/{$model}.php", $modelContent);
        echo "✅ Modèle '$model' avec méthodes CRUD créé.\n";
    }

    /**
     * Récupère les clés étrangères de la table.
     *
     * @param PDO $pdo
     * @param string $table
     * @return array
     */
    private function getForeignKeys(PDO $pdo, string $table): array
    {
        $foreignKeys = [];

        // Requête pour récupérer les clés étrangères
        $stmt = $pdo->prepare("
            SELECT 
                COLUMN_NAME, 
                REFERENCED_TABLE_NAME, 
                REFERENCED_COLUMN_NAME 
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = :table 
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $stmt->execute(['table' => $table]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formater les résultats
        foreach ($results as $result) {
            $foreignKeys[$result['COLUMN_NAME']] = [
                'table' => $result['REFERENCED_TABLE_NAME'],
                'column' => $result['REFERENCED_COLUMN_NAME'],
            ];
        }

        return $foreignKeys;
    }
}