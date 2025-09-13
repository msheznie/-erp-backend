<?php
/**
 * =============================================
 * -- File Name : SupplierImportance.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Importance
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierImportance
 * @package App\Models
 * @version February 28, 2018, 4:16 am UTC
 *
 * @property string importanceDescription
 */
class SupplierImportance extends Model
{
    //use SoftDeletes;

    public $table = 'supplierimportance';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'importanceDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierImportanceID' => 'integer',
        'importanceDescription' => 'string'
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
    protected $appends = ['importanceDescription'];

    /**
     * Get the description based on current language.
     *
     * @return string
     */
    public function getImportanceDescriptionAttribute()
    {
        $languageCode = app()->getLocale();

        $translation = $this->translations()
            ->where('languageCode', $languageCode)
            ->first();

        return $translation ? $translation->importanceDescription : $this->attributes['importanceDescription'];
    }

    /**
     * Get the translations for the supplier importance.
     */
    public function translations()
    {
        return $this->hasMany(SupplierImportanceTranslation::class, 'supplierImportanceID', 'supplierImportanceID');
    }
    
}
