<?php
/**
 * =============================================
 * -- File Name : SupplierImportanceTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Importance Translation
 * -- Author : System Generated
 * -- Create date : 13- September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierImportanceTranslation
 * @package App\Models
 * @version September 13, 2025, 1:00 pm UTC
 *
 * @property integer supplierImportanceID
 * @property string languageCode
 * @property string importanceDescription
 */
class SupplierImportanceTranslation extends Model
{
    //use SoftDeletes;

    public $table = 'supplierimportance_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'supplierImportanceID',
        'languageCode',
        'importanceDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supplierImportanceID' => 'integer',
        'languageCode' => 'string',
        'importanceDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supplierImportanceID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'importanceDescription' => 'required|string|max:255'
    ];

    /**
     * Get the supplier importance that owns the translation.
     */
    public function supplierImportance()
    {
        return $this->belongsTo(SupplierImportance::class, 'supplierImportanceID', 'supplierImportanceID');
    }
}
