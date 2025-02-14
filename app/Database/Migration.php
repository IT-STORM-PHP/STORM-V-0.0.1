<?php

namespace App\Database;

use App\Models\Database;
use App\Schema\Blueprint;
use PDO;

abstract class Migration
{
    protected PDO $pdo;
    protected string $tableName;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tableName = $this->getTableName();
    }

    abstract public function up();
    abstract public function down();

    public function migrate()
    {
        $table = new Blueprint($this->tableName);
        $this->up($table);
        $sql = $table->getSQL();
        $this->executeSQL($sql);
    }

    public function rollback()
    {
        $table = new Blueprint($this->tableName);
        $this->down($table);
        $sql = $table->dropSQL();
        $this->executeSQL($sql);
    }

    protected function executeSQL(string $sql)
    {
        try {
            $this->pdo->exec($sql);
            echo "✅ Migration exécutée : " . $this->tableName . "\n";
        } catch (\PDOException $e) {
            // Vérifier si l'erreur est liée à une table déjà existante
            if (strpos($e->getMessage(), '1050 Table') !== false && strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠️ La table '{$this->tableName}' existe déjà, migration ignorée.\n";
            } else {
                // Afficher l'erreur pour les autres cas
                echo "❌ Erreur de migration ({$this->tableName}) : " . $e->getMessage() . "\n";
            }
        }
    }

    private function getTableName(): string
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }
}
