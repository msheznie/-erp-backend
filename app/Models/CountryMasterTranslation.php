<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CountryMasterTranslation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryName",
 *          description="countryName",
 *          type="string"
 *      )
 * )
 */
class CountryMasterTranslation extends Model
{

    public $table = 'countrymaster_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'countryID',
        'languageCode',
        'countryName'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'countryID' => 'integer',
        'languageCode' => 'string',
        'countryName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Get the country that owns the translation.
     */
    public function country()
    {
        return $this->belongsTo(CountryMaster::class, 'countryID', 'countryID');
    }
}
