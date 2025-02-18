<?php
namespace App\Models;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = require __DIR__ . '/../../config/config.php';

        // Vérifier si le nom de la base de données est vide ou null
        if (empty($config['db_name'])) {
            die("❌ ERREUR : Veuillez spécifier un nom de base de données dans le fichier de configuration.\n");
        }

        // Connexion à MySQL sans spécifier de base de données
        $dsnNoDb = "mysql:host={$config['db_host']};port={$config['db_port']};charset=utf8mb4";
        $pdoNoDb = new \PDO($dsnNoDb, $config['db_user'], $config['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        $dbName = $config['db_name'];

        // Vérifier si la base de données existe
        $stmt = $pdoNoDb->query("SHOW DATABASES LIKE '$dbName'");
        if ($stmt->rowCount() === 0) {
            // La base de données n'existe pas, on la crée
            echo "⚠️ La base de données '$dbName' n'existe pas. Création en cours...\n";
            $pdoNoDb->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✅ Base de données '$dbName' créée avec succès.\n";
        }

        // Connexion à la base de données spécifiée
        $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$dbName};charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $config['db_user'], $config['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>
