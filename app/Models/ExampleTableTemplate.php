<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExampleTableTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="data",
 *          description="data",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ExampleTableTemplate extends Model
{

    public $table = 'example_table_template';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemID',
        'data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'data' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['data'];

    /**
     * Get the data with translated column headers based on current language.
     *
     * @return string
     */
    public function getDataAttribute()
    {
        $languageCode = app()->getLocale();

        // Get column header translations
        $columnTranslations = $this->getColumnTranslations($languageCode);

        // Get original data
        $originalData = $this->attributes['data'];

        if (empty($columnTranslations) || empty($originalData)) {
            return $originalData;
        }

        // Translate column headers in the JSON data
        $translatedData = $this->translateColumnHeaders($originalData, $columnTranslations);

        return $translatedData;
    }

    /**
     * Get column header translations for the specified language.
     *
     * @param string $languageCode
     * @return array
     */
    private function getColumnTranslations($languageCode)
    {
        $translation = \App\Models\ExampleTableTemplateTranslation::where('languageCode', $languageCode)
            ->where('example_table_template_id', 0) // Special ID for column header translations
            ->first();

        return $translation ? json_decode($translation->data, true) : [];
    }


    /**
     * Recursively translate array keys.
     *
     * @param array $data
     * @param array $translations
     */
    private function translateArrayKeys(&$data, $translations)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($key) && isset($translations[$key])) {
                    $newKey = $translations[$key];
                    $data[$newKey] = $value;
                    unset($data[$key]);
                }

                if (is_array($value)) {
                    $this->translateArrayKeys($value, $translations);
                }
            }
        }
    }

    /**
     * Translate column headers in JSON data for nested array structure.
     *
     * @param string $jsonData
     * @param array $translations
     * @return string
     */
    private function translateColumnHeaders($jsonData, $translations)
    {
        $data = json_decode($jsonData, true);

        if (!is_array($data)) {
            return $jsonData;
        }

        // Handle the nested array structure: [[{object1}, {object2}, ...]]
        if (is_array($data) && count($data) > 0 && is_array($data[0])) {
            foreach ($data as &$row) {
                if (is_array($row)) {
                    foreach ($row as &$item) {
                        if (is_array($item)) {
                            $this->translateArrayKeys($item, $translations);
                        }
                    }
                }
            }
        } else {
            // Handle flat array structure
            $this->translateArrayKeys($data, $translations);
        }

        return json_encode($data);
    }

    /**
     * Get the translations for the example table template.
     */
    public function translations()
    {
        return $this->hasMany(ExampleTableTemplateTranslation::class, 'example_table_template_id', 'id');
    }
}
