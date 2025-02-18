<?php

namespace App\Console\Commands;

use App\Models\Database;
use PDO;

class MakeMigration
{
    public function makeMigration($name)
    {
        if (!$name) {
            echo "❌ Veuillez fournir un nom de migration.\n";
            exit(1);
        }

        // Utilisation du nom directement sans datation
        $filename = "database/migrations/{$name}.php";
        $classname = ucfirst($name);

        // Contenu de la migration
        $content = "<?php\n\nnamespace Database\Migrations;\n\nuse App\Schema\Blueprint;\nuse App\Database\Migration;\n\nclass {$classname} extends Migration\n{\n";
        $content .= "    public function up()\n    {\n";
        $content .= "        \$table = new Blueprint('$name');\n";
        $content .= "        \$table->id();\n";
        $content .= "        \$table->string('name');\n";
        $content .= "        \$table->timestamps();\n";
        $content .= "        \$this->executeSQL(\$table->updateOrCreate());\n";
        $content .= "    }\n\n";
        $content .= "    public function down()\n    {\n";
        $content .= "        \$table = new Blueprint('$name');\n";
        $content .= "        \$this->executeSQL(\$table->dropSQL());\n";
        $content .= "    }\n}\n";

        // Vérifier si le dossier de migration existe
        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0777, true);
        }

        // Créer le fichier de migration et y écrire le contenu
        file_put_contents($filename, $content);
        echo "✅ Migration créée : $filename\n";
    }
}
