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
        $this->columns[] = "`$column` INT AUTO_INCREMENT PRIMARY KEY";
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

    // ALTER TABLE : Modifier une colonne
    public function modifyColumn(string $column, string $type, int $length = null)
    {
        $lengthSQL = $length ? "($length)" : "";
        $this->alterations[] = "MODIFY COLUMN `$column` $type$lengthSQL NOT NULL";
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

    // Génération du SQL de suppression
    public function dropSQL(): string
    {
        return "DROP TABLE IF EXISTS `{$this->table}`;";
    }
}
