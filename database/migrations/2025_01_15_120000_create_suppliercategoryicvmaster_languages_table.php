<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliercategoryicvmasterLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('suppliercategoryicvmaster_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supCategoryICVMasterID')->unsigned();
            $table->string('languageCode', 10);
            $table->string('categoryDescription', 255);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliercategoryicvmaster_languages');
    }
}
