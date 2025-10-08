<?php
/**
 * =============================================
 * -- File Name : SupplierType.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Type
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierType
 * @package App\Models
 * @version February 28, 2018, 4:18 am UTC
 *
 * @property string typeDescription
 */
class SupplierType extends Model
{
    //use SoftDeletes;

    public $table = 'suppliertype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'typeDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierTypeID' => 'integer',
        'typeDescription' => 'string'
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
    protected $appends = ['typeDescription'];

    /**
     * Get the description based on current language.
     *
     * @return string
     */
    public function getTypeDescriptionAttribute()
    {
        $languageCode = app()->getLocale();

        $translation = $this->translations()
            ->where('languageCode', $languageCode)
            ->first();

        return $translation ? $translation->typeDescription : $this->attributes['typeDescription'];
    }

    /**
     * Get the translations for the supplier type.
     */
    public function translations()
    {
        return $this->hasMany(SupplierTypeTranslation::class, 'supplierTypeID', 'supplierTypeID');
    }

}
