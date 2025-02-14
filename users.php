<?php

namespace Database\Migrations;

use App\Schema\Blueprint;
use App\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // Création de la table avec le nom spécifié
        $table = new Blueprint('users');
        $table->id('id');  // Création de la colonne ID (clé primaire)
        // Ajout de la colonne nom de type string
        $table->string('nom');
        // Ajout de la colonne email de type string
        $table->string('email');
        // Ajout de la colonne password de type string
        $table->string('password');  // Champ mot de passe
        $table->timestamps();
        $this->executeSQL($table->getSQL());
    }

    public function down()
    {
        $table = new Blueprint('users');
        $this->executeSQL($table->dropSQL());
    }
}
