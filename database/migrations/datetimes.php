<?php

namespace Database\Migrations;

use App\Schema\Blueprint;
use App\Database\Migration;

class Datetimes extends Migration
{
    public function up()
    {
        $table = new Blueprint('datetimes');
        $table->id();
        $table->string('codAricle');
        $table->string('nomArticle');
        $table->timestamps();
        $this->executeSQL($table->getSQL());
    }

    public function down()
    {
        $table = new Blueprint('datetimes');
        $this->executeSQL($table->dropSQL());
    }
}
