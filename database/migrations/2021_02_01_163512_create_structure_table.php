<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;

class CreateStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('structure')) { return; }
        Schema::create('structure', function (Blueprint $collection) {
            $collection->string('siteSIRET')->nullable();
            $collection->string('siteSIREN')->nullable();
            $collection->string('siteFINESS')->nullable();
            $collection->string('legalEstablishmentFINESS')->nullable();
            $collection->string('structureTechnicalId')->unique()->index();  // Id
            $collection->string('legalCommercialName')->nullable();
            $collection->string('publicCommercialName')->nullable();
            $collection->string('recipientAdditionalInfo')->nullable();
            $collection->string('geoLocationAdditionalInfo')->nullable();
            $collection->string('streetNumber')->nullable();
            $collection->string('streetNumberRepetitionIndex')->nullable();
            $collection->string('streetCategoryCode')->nullable();
            $collection->string('streetLabel')->nullable();
            $collection->string('distributionMention')->nullable();
            $collection->string('cedexOffice')->nullable();
            $collection->string('postalCode')->nullable();
            $collection->string('communeCode')->nullable();
            $collection->string('countryCode')->nullable();
            $collection->string('phone')->nullable();
            $collection->string('phone2')->nullable();
            $collection->string('fax')->nullable();
            $collection->string('email')->nullable();
            $collection->string('departmentCode')->nullable();
            $collection->string('oldStructureId')->nullable();
            $collection->string('registrationAuthority')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('structure');
    }
}
