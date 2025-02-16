<?php

namespace Database\Migrations;

use App\Schema\Blueprint;
use App\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $table = new Blueprint('users');
        $table->id();
        $table->string('email', 100);
        $table->string('password', 255);
        $table->string('nom', 100);
        $table->string('prenom', 100);
        $table->string('role', 50);
        $table->unique('email');
        $table->timestamps();
        $this->executeSQL($table->getSQL());
    }

    public function down()
    {
        $table = new Blueprint('users');
        $this->executeSQL($table->dropSQL());
    }
}
