<?php

namespace App\Console;

use App\Console\Commands\Crud\MakeCrud;
use PDO;
use App\Models\Database;
use App\Console\Commands\Login\MakeLogin;
use App\Console\Commands\MakeMigration;
use App\Console\Commands\Migrate;
use App\Console\Commands\MakeController;
class Kernel 
{
    private $makeLogin, $makeMigration, $migrate, $makeController, $makeCrud;
    public function __construct(){
        $this->makeLogin = new MakeLogin();
        $this->makeMigration = new MakeMigration();
        $this->migrate = new Migrate();
        $this->makeController = new MakeController();
        $this->makeCrud = new MakeCrud();
    }
    protected array $commands = [
        'serve' => 'serve',
        'make:migration' => 'makeMigration',
        'migrate' => 'migrate',
        'migrateTest' => 'migrateTest',
        'rollback' => 'rollback',
        'make:crud' => 'makeCrud',
        'make:controller' => 'makeController',
        'make:login' => 'makeLogin',

    ];

    public function handle($argv)
    {
        $command = $argv[1] ?? null;
        $argument = $argv[2] ?? null;

        if (!$command || !isset($this->commands[$command])) {
            $this->showUsage();
            exit(1);
        }

        $method = $this->commands[$command];
        $this->$method($argument);
    }

    protected function makeLogin()
    {
        $this->makeLogin->run();
    }


    protected function makeMigration($name){
        $this->makeMigration->makeMigration($name);
    }
    
    protected function makeController($controllerName){
        $this->makeController->makeController($controllerName);
    }

    protected function migrate(){
        $this->migrate->migrate();
    }

    protected function makeCrud($model){
        $this->makeCrud->makeCrud($model);
    }

    protected function serve()
    {
        global $argv;

        $host = "127.0.0.1"; // Valeur par défaut
        $port = 8000;        // Valeur par défaut

        foreach ($argv as $arg) {
            if (strpos($arg, '--host=') === 0) {
                $host = substr($arg, 7);
            } elseif (strpos($arg, '--port=') === 0) {
                $port = (int) substr($arg, 7);
            }
        }

        while (!@stream_socket_server("tcp://$host:$port")) {
            $port++; // Incrémente si le port est occupé
        }

        $cmd = "php -S $host:$port -t public";
        echo "Serveur démarré sur http://$host:$port\n";
        exec($cmd);
    }

    

    
    protected function rollback()
    {
        echo "⏪ Annulation des dernières migrations...\n";
        $files = glob(__DIR__ . '/../../database/migrations/*.php');
        rsort($files); // Exécute les rollbacks en ordre inverse

        foreach ($files as $file) {
            require_once $file;
            $className = 'Database\\Migrations\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className)) {
                $migration = new $className();
                echo "🔄 Rollback : " . $className . "\n";
                $migration->down();
            }
        }
        echo "✅ Rollback terminé.\n";
    }

    
    



    protected function showUsage()
    {
        echo "Usage: php storm <commande>\n";
        echo "Commandes disponibles :\n";
        echo "  serve             Démarrer le serveur local\n";
        echo "  make:migration   Créer un fichier de migration\n";
        echo "  migrate           Exécuter les migrations\n";
        //echo "  migrateTest       Exécuter les migrations avec possibilité de mise à jour des tables (test) \n";
        echo "  rollback          Annuler la dernière migration\n";
        echo '  make:crud         Créer un modèle et un contrôleur CRUD pour une table existante' . "\n";
        echo '  make:controller  Créer un contrôleur' . "\n";
        echo '  make:login        Créer un système de connexion avec une table existante' . "\n";
        echo '  make:model        Créer un modèle' . "\n";
    }
}
