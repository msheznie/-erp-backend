<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSuppliercategoryicvmasterToSuppliercategoryicvmasterLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $translations = [
            [
                'supCategoryICVMasterID' => 1,
                'translations' => [
                    'ar' => 'المنتج',
                    'en' => 'Product'
                ]
            ],
            [
                'supCategoryICVMasterID' => 2,
                'translations' => [
                    'ar' => 'الخدمات',
                    'en' => 'Services'
                ]
            ]
        ];

         foreach ($translations as $entry) {
            foreach ($entry['translations'] as $languageCode => $translation) {
                DB::table('suppliercategoryicvmaster_languages')->insert([
                    'supCategoryICVMasterID' => $entry['supCategoryICVMasterID'],
                    'languageCode' => $languageCode,
                    'categoryDescription' => $translation,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('suppliercategoryicvmaster_languages')->whereIn('supCategoryICVMasterID', [1, 2])->delete();
    }
}
