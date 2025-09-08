<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthsLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('months_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('monthID');
            $table->string('languageCode', 10);
            $table->string('monthDes', 255);
            $table->timestamps();
            
            $table->foreign('monthID')->references('monthID')->on('months')->onDelete('cascade');
            $table->index(['monthID', 'languageCode']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('months_languages');
    }
}
