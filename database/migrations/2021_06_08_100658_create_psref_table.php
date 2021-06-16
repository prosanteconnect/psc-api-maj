<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;

class CreatePsrefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('psref')) { return; }
        Schema::create('psref', function (Blueprint $table) {
            $table->string('nationalIdRef')->unique();
            $table->string('nationalId')->index();
            $table->date('activated');
            $table->date('deactivated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psref');
    }
}
