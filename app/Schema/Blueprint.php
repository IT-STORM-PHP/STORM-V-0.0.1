<?php

namespace App\Schema;

class Blueprint
{
    private string $table;
    private array $columns = [];
    private ?string $primaryKey = null;
    private array $foreignKeys = [];
    private array $uniqueConstraints = [];
    private array $alterations = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    // Clé primaire auto-incrémentée
    public function id(string $column = 'id')
    {
        $this->primaryKey = $column;
        $this->columns[$column] = "`$column` INT AUTO_INCREMENT PRIMARY KEY";
    }

    // Types de colonnes
    public function string(string $column, int $length = 255)
    {
        $this->columns[$column] = "`$column` VARCHAR($length) NOT NULL";
    }

    public function integer(string $column)
    {
        $this->columns[$column] = "`$column` INT NOT NULL";
    }

    public function boolean(string $column)
    {
        $this->columns[$column] = "`$column` TINYINT(1) NOT NULL";
    }

    public function text(string $column)
    {
        $this->columns[$column] = "`$column` TEXT NOT NULL";
    }

    public function decimal(string $column, int $precision = 10, int $scale = 2)
    {
        $this->columns[$column] = "`$column` DECIMAL($precision, $scale) NOT NULL";
    }

    public function date(string $column)
    {
        $this->columns[$column] = "`$column` DATE NOT NULL";
    }

    public function datetime(string $column)
    {
        $this->columns[$column] = "`$column` DATETIME NOT NULL";
    }

    public function enum(string $column, array $values)
    {
        $escapedValues = implode(", ", array_map(fn($v) => "'$v'", $values));
        $this->columns[$column] = "`$column` ENUM($escapedValues) NOT NULL";
    }

    // Ajout de timestamps (created_at & updated_at) + Trigger pour empêcher modification de created_at
    public function timestamps()
    {
        $this->columns["created_at"] = "`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
        $this->columns["updated_at"] = "`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    // Clés étrangères avec gestion des contraintes
    public function foreign(string $column, string $references, string $onTable, ?string $onDelete = null, ?string $onUpdate = null)
    {
        $constraint = "FOREIGN KEY (`$column`) REFERENCES `$onTable`(`$references`)";
        if ($onDelete) {
            $constraint .= " ON DELETE $onDelete";
        }
        if ($onUpdate) {
            $constraint .= " ON UPDATE $onUpdate";
        }
        $this->foreignKeys[] = $constraint;
    }

    // Ajout d'options aux colonnes
    public function nullable(string $column)
    {
        if (isset($this->columns[$column])) {
            $this->columns[$column] = str_replace("NOT NULL", "NULL", $this->columns[$column]);
        }
    }

    public function default(string $column, mixed $value)
    {
        if (isset($this->columns[$column])) {
            $defaultValue = is_string($value) ? "'$value'" : $value;
            $this->columns[$column] .= " DEFAULT $defaultValue";
        }
    }

    public function unique(string $column)
    {
        $this->uniqueConstraints[] = "UNIQUE (`$column`)";
    }

    // ALTER TABLE : Ajouter une colonne
    public function addColumn(string $column, string $type, int $length = null)
    {
        $lengthSQL = $length ? "($length)" : "";
        $this->alterations[] = "ADD COLUMN `$column` $type$lengthSQL NOT NULL";
    }


    // ALTER TABLE : Ajouter une colonne avec 'AFTER' pour spécifier la position
    public function addColumnAfter(string $column, string $type, string $afterColumn, int $length = null)
    {
        $lengthSQL = $length ? "($length)" : "";
        $this->alterations[] = "ADD COLUMN `$column` $type$lengthSQL NOT NULL AFTER `$afterColumn`";
    }

    // Exemple d'appel pour ajouter une colonne après une autre
    public function after(string $column, string $type, string $afterColumn, int $length = null)
    {
        $this->addColumnAfter($column, $type, $afterColumn, $length);
    }

    // ALTER TABLE : Modifier une colonne
    public function modifyColumn(string $column, string $type, string $afterColumn = null, int $length = null)
    {
        // Construire la portion SQL pour la longueur si elle est définie
        $lengthSQL = $length ? "($length)" : "";

        // Vérifier si un 'after' est spécifié
        if ($afterColumn) {
            // Ajouter la colonne avec la clause 'AFTER'
            $this->alterations[] = "MODIFY COLUMN `$column` $type$lengthSQL NOT NULL AFTER `$afterColumn`";
        } else {
            // Sinon, on modifie la colonne sans spécifier de position (par défaut, à la fin)
            $this->alterations[] = "MODIFY COLUMN `$column` $type$lengthSQL NOT NULL";
        }
    }


    // ALTER TABLE : Renommer une colonne
    public function renameColumn(string $oldName, string $newName, string $type = "VARCHAR(255)")
    {
        $this->alterations[] = "CHANGE COLUMN `$oldName` `$newName` $type NOT NULL";
    }

    // ALTER TABLE : Supprimer une colonne
    public function dropColumn(string $column)
    {
        $this->alterations[] = "DROP COLUMN `$column`";
    }

    // ALTER TABLE : Ajouter un index
    public function addIndex(string $column)
    {
        $this->alterations[] = "ADD INDEX (`$column`)";
    }

    // Génération du SQL de création de table
    public function getSQL(): string
    {
        $columnsSQL = implode(", ", $this->columns);
        $foreignKeysSQL = !empty($this->foreignKeys) ? ", " . implode(", ", $this->foreignKeys) : "";
        $uniqueSQL = !empty($this->uniqueConstraints) ? ", " . implode(", ", $this->uniqueConstraints) : "";

        // Générer le SQL pour créer la table
        $createTableSQL = "CREATE TABLE `{$this->table}` ($columnsSQL $foreignKeysSQL $uniqueSQL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        // Générer le trigger
        $triggerSQL = $this->addTriggers();

        // Retourner le SQL complet : création de la table suivi de la création du trigger
        return $createTableSQL . "\n" . $triggerSQL;
    }

    // Génération du SQL pour empêcher la modification de created_at
    public function addTriggers(): string
    {
        return "
            
            CREATE TRIGGER `prevent_update_created_at`
            BEFORE UPDATE ON `{$this->table}`
            FOR EACH ROW
            BEGIN
                SET NEW.created_at = OLD.created_at;
            END;
        ";
    }

    // Génération du SQL de modification (ALTER TABLE)
    public function getAlterSQL(): string
    {
        if (empty($this->alterations)) {
            return "";
        }
        return "ALTER TABLE `{$this->table}` " . implode(", ", $this->alterations) . ";";
    }

    // Génération du SQL de mise à jour ou de création de la table
    public function updateOrCreate(): string
    {
        // Vérifier si la table existe
        $query = "SHOW TABLES LIKE '" . $this->table . "';";

        if (!$this->executeQuery($query)->fetch()) {
            // Si la table n'existe pas, la créer
            return $this->getSQL();
        }

        // Récupérer les colonnes existantes
        $query = "SHOW COLUMNS FROM `{$this->table}`";
        $existingColumns = [];
        $result = $this->executeQuery($query);

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $existingColumns[$row['Field']] = $row;
        }

        // Variable pour garder une trace de la dernière colonne ajoutée
        $lastAddedColumn = null;

        // Générer les modifications nécessaires
        foreach ($this->columns as $column => $definition) {
            //echo($column . " : " . $this->extractType($definition) . "\n" . json_encode($existingColumns[$column]) . "\n" . strtoupper($existingColumns[$column]["Type"]) . "\n");
            // Vérifier si la colonne existe déjà
            if (!isset($existingColumns[$column])) {
                // Nouvelle colonne
                //$this->addColumn($column, $this->extractType($definition));
                // Nouvelle colonne : si après la précédente colonne ajoutée
                if ($lastAddedColumn) {
                    // Ajouter la colonne après la dernière colonne ajoutée
                    $this->addColumnAfter($column, $this->extractType($definition), $lastAddedColumn);
                } else {
                    // Sinon, ajouter la colonne à la fin de la table
                    $this->addColumn($column, $this->extractType($definition));
                }

            } elseif (isset($this->columns[$column]) && strtoupper($existingColumns[$column]['Type']) === $this->extractType($definition)) {
                // Si la colonne existe et avec le type parfaitement identique, passer au suivant
                //echo(($this->columns[$column]) . " 1### " . strtoupper($existingColumns[$column]['Type']) . " 2### " . $this->extractType($definition)."###\n\n" );
            } elseif (strtoupper($existingColumns[$column]['Type']) !== $this->extractType($definition)) {
                // Si la colonne existe mais avec un type différent
                echo "La colonne '$column' existe déjà avec un type différent.\n";
                echo "Souhaitez-vous :\n";
                echo "\t 0. La modifier directement\n";
                echo "\t 1. La supprimer et la recréer\n";
        
                // Boucle pour garantir un choix valide (0 ou 1)
                $choice = null;
                while ($choice !== '0' || $choice !== '1' || $choice !== '') {        
                    //Par defaut 0
                    $choice = '0';
                    // Demander le choix
                    echo "Entrez le numéro de votre choix (par defaut 0) : ";
                    $choice = trim(fgets(STDIN));
                    
                    // Vérifier si le choix est valide
                    if ($choice === '1') {
                        echo "Suppression de la colonne '$column'...\n";
                        $this->dropColumn($column); // Supprimer la colonne
                        //$this->addColumn($column, $this->extractType($definition)); // Recréer la colonne
                        // Recréer la colonne (avec ou sans 'after' selon la définition)
                        if ($lastAddedColumn) {
                            // Ajouter après la dernière colonne modifiée
                            $this->addColumnAfter($column, $this->extractType($definition), $lastAddedColumn);
                        } else {
                            // Ajouter à la fin si pas d'autres colonnes
                            $this->addColumn($column, $this->extractType($definition));
                        }
                        break;
                    } elseif ($choice === '0' || $choice === '') {
                        echo "Modification du type de la colonne '$column'...\n";
                        $this->modifyColumn($column, $this->extractType($definition), $lastAddedColumn ); // Modifier la colonne
                        break;
                    } else {
                        echo "Choix invalide. Veuillez entrer 0 ou 1.\n";
                    }

                }
            }
            
            // Mettre à jour la dernière colonne modifiée ou ajoutée
            $lastAddedColumn = $column;
        }
        
        // Supprimer les colonnes qui ne sont plus définies
        foreach ($existingColumns as $column => $data) {
            if (!isset($this->columns[$column])) {
                $test = ($this->dropColumn($column));
            }
        }
        return $this->getAlterSQL();
    }

    // Extraire uniquement le type de colonne pour la comparaison
    private function extractType(string $definition): string
    {
        preg_match('/`.+`\s+([A-Z]+(\([0-9,]+\))?)/i', $definition, $matches);
        return $matches[1] ?? '';
    }

    private function executeQuery(string $query): \PDOStatement
    {
        $pdo = \App\Models\Database::getInstance()->getConnection(); // À adapter selon ton implémentation
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    // Génération du SQL de suppression
    public function dropSQL(): string
    {
        return "DROP TABLE IF EXISTS `{$this->table}`;";
    }
}
