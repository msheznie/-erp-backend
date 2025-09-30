<?php
/**
 * =============================================
 * -- File Name : AllocationMaster.php
 * -- Project Name : ERP
 * -- Module Name : Chart Of Account
 * -- Author : Mohamed Rilwan
 * -- Create date : 07- Nov 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AllocationMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="AutoID",
 *          description="AutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Desciption",
 *          description="Desciption",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DesCode",
 *          description="DesCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesstamp",
 *          description="timesstamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class AllocationMaster extends Model
{

    public $table = 'erp_allocation_master';
    protected $primaryKey  = 'AutoID';
    public $timestamps = false;

    public $fillable = [
        'Desciption',
        'isActive',
        'DesCode',
        'timesstamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'AutoID' => 'integer',
        'isActive' => 'integer',
        'Desciption' => 'string',
        'DesCode' => 'string',
        'timesstamp' => 'datetime'
    ];

    public static $rules = [

    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['Desciption'];

    /**
     * Get the description based on current language.
     *
     * @return string
     */
    public function getDesciptionAttribute()
    {
        $languageCode = app()->getLocale();

        $translation = $this->translations()
            ->where('languageCode', $languageCode)
            ->first();

        return $translation ? $translation->Desciption : $this->attributes['Desciption'];
    }

    /**
     * Get the translations for the allocation master.
     */
    public function translations()
    {
        return $this->hasMany(AllocationMasterTranslation::class, 'AutoID', 'AutoID');
    }
}
