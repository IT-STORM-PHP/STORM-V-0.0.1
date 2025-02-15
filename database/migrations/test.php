<?php

namespace Database\Migrations;

use App\Schema\Blueprint;
use App\Database\Migration;

class Test extends Migration
{
    public function up()
    {
        $table = new Blueprint('test');
        $table->id();
        $table->string('name');
        $table->integer('age');
        $table->timestamps();
        $this->executeSQL($table->getSQL());
    }

    public function down()
    {
        $table = new Blueprint('test');
        $this->executeSQL($table->dropSQL());
    }
}
