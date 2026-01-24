<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Check if uuid column doesn't exist before adding
            if (!Schema::hasColumn('failed_jobs', 'uuid')) {
                $table->string('uuid')->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('failed_jobs', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }
};
