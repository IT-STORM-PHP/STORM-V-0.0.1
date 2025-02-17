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
        $table->string(column: 'username');
        $table->string('nom');
        $table->string('prenom');
        $table->string('email');
        $table->string('password');
        $table->unique("email");
        $table->unique("password");
        $table->timestamps();
        $this->executeSQL($table->updateOrCreate());
    }

    public function down()
    {
        $table = new Blueprint('users');
        $this->executeSQL($table->dropSQL());
    }
}
