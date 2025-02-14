<?php

namespace App\Console\Commands;

use Exception;
use PDO;

class MakeModel
{
    public function execute()
    {
        echo "Nom de la table : ";
        $tableName = trim(fgets(STDIN));

        if (empty($tableName)) {
            echo "Erreur : Le nom de la table ne peut pas être vide.\n";
            return;
        }

        // Vérification de l'existence de la table dans la base de données
        if (!$this->tableExists($tableName)) {
            echo "Erreur : La table '$tableName' n'existe pas dans la base de données.\n";
            return;
        }

        // Récupérer les colonnes de la table
        $columns = $this->getTableColumns($tableName);

        // Générer le modèle avec les méthodes CRUD
        $this->generateModel($tableName, $columns);

        echo "✅ Modèle généré avec succès : app/Models/" . ucfirst($tableName) . ".php\n";
    }

    // Vérifier si la table existe dans la base de données
    private function tableExists($tableName)
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=nom_de_la_base', 'username', 'password');
            $query = "SHOW TABLES LIKE :table";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['table' => $tableName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
            return false;
        }
    }

    // Récupérer les colonnes de la table
    private function getTableColumns($tableName)
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=nom_de_la_base', 'username', 'password');
            $query = "DESCRIBE $tableName";
            $stmt = $pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erreur lors de la récupération des colonnes : " . $e->getMessage();
            return [];
        }
    }

    // Générer le modèle avec les méthodes CRUD
    private function generateModel($tableName, $columns)
    {
        $modelName = ucfirst($tableName);
        $modelPath = "app/Models/{$modelName}.php";

        // Vérifier si le modèle existe déjà
        if (file_exists($modelPath)) {
            echo "Erreur : Le modèle {$modelName} existe déjà.\n";
            return;
        }

        // Générer les méthodes CRUD
        $crudMethods = $this->generateCrudMethods($columns);

        // Générer le contenu du modèle
        $modelContent = "<?php\n\n";
        $modelContent .= "namespace App\Models;\n\n";
        $modelContent .= "use App\Core\Model;\n\n";
        $modelContent .= "class {$modelName} extends Model\n{\n";
        $modelContent .= "    protected \$table = '$tableName';\n\n";
        $modelContent .= "    // Méthodes CRUD\n";
        $modelContent .= $crudMethods;
        $modelContent .= "}\n";

        // Créer le fichier du modèle
        file_put_contents($modelPath, $modelContent);
    }

    // Générer les méthodes CRUD en fonction des colonnes de la table
    private function generateCrudMethods($columns)
    {
        $methods = "";

        // Méthode pour récupérer tous les enregistrements
        $methods .= "    public static function all()\n";
        $methods .= "    {\n";
        $methods .= "        return self::query('SELECT * FROM ' . \$this->table);\n";
        $methods .= "    }\n\n";

        // Méthode pour récupérer un enregistrement par ID
        $methods .= "    public static function find(\$id)\n";
        $methods .= "    {\n";
        $methods .= "        return self::query('SELECT * FROM ' . \$this->table . ' WHERE id = :id', ['id' => \$id])->fetch();\n";
        $methods .= "    }\n\n";

        // Méthode pour créer un nouvel enregistrement
        $methods .= "    public function create(\$data)\n";
        $methods .= "    {\n";
        $methods .= "        \$fields = implode(',', array_keys(\$data));\n";
        $methods .= "        \$placeholders = ':' . implode(',:', array_keys(\$data));\n";
        $methods .= "        \$sql = 'INSERT INTO ' . \$this->table . ' (\$fields) VALUES (\$placeholders)';\n";
        $methods .= "        return self::query(\$sql, \$data);\n";
        $methods .= "    }\n\n";

        // Méthode pour mettre à jour un enregistrement par ID
        $methods .= "    public function update(\$id, \$data)\n";
        $methods .= "    {\n";
        $methods .= "        \$set = [];\n";
        foreach ($columns as $col) {
            if ($col['Field'] !== 'id') {
                $methods .= "        \$set[] = '{$col['Field']} = :{$col['Field']}';\n";
            }
        }
        $methods .= "        \$setString = implode(', ', \$set);\n";
        $methods .= "        \$sql = 'UPDATE ' . \$this->table . ' SET ' . \$setString . ' WHERE id = :id';\n";
        $methods .= "        \$data['id'] = \$id;\n";
        $methods .= "        return self::query(\$sql, \$data);\n";
        $methods .= "    }\n\n";

        // Méthode pour supprimer un enregistrement par ID
        $methods .= "    public function delete(\$id)\n";
        $methods .= "    {\n";
        $methods .= "        \$sql = 'DELETE FROM ' . \$this->table . ' WHERE id = :id';\n";
        $methods .= "        return self::query(\$sql, ['id' => \$id]);\n";
        $methods .= "    }\n";

        return $methods;
    }
}
