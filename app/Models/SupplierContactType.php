<?php
/**
 * =============================================
 * -- File Name : SupplierContactType.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Contact Type
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierContactType
 * @package App\Models
 * @version March 6, 2018, 10:53 am UTC
 *
 * @property string supplierContactDescription
 * @property string|\Carbon\Carbon timestamp
 */
class SupplierContactType extends Model
{
    //use SoftDeletes;

    public $table = 'suppliercontacttype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'supplierContactDescription',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierContactTypeID' => 'integer',
        'supplierContactDescription' => 'string'
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
    protected $appends = ['supplierContactDescription'];

    /**
     * Get the supplier contact description based on current language.
     *
     * @return string
     */
    public function getSupplierContactDescriptionAttribute()
    {
        $languageCode = app()->getLocale();

        $translation = $this->translations()
            ->where('languageCode', $languageCode)
            ->first();

        return $translation ? $translation->supplierContactDescription : $this->attributes['supplierContactDescription'];
    }

    /**
     * Get the translations for the supplier contact type.
     */
    public function translations()
    {
        return $this->hasMany(SupplierContactTypeTranslation::class, 'supplierContactTypeID', 'supplierContactTypeID');
    }
    
}
