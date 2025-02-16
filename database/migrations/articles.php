<?php

namespace Database\Migrations;

use App\Schema\Blueprint;
use App\Database\Migration;

class Articles extends Migration
{
    public function up()
    {
        $table = new Blueprint('articles');
        $table->id();
        $table->string('nom', 100);
        $table->text('description');
        $table->integer('prix');
        $table->integer('quantite');
        $table->timestamps();
        $this->executeSQL($table->getSQL());
    }

    public function down()
    {
        $table = new Blueprint('articles');
        $this->executeSQL($table->dropSQL());
    }
}
