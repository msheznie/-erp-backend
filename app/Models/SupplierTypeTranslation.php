<?php
/**
 * =============================================
 * -- File Name : SupplierTypeTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Type Translation
 * -- Author : System Generated
 * -- Create date : 13- September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierTypeTranslation
 * @package App\Models
 * @version September 13, 2025, 12:00 pm UTC
 *
 * @property integer supplierTypeID
 * @property string languageCode
 * @property string typeDescription
 */
class SupplierTypeTranslation extends Model
{
    //use SoftDeletes;

    public $table = 'suppliertype_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'supplierTypeID',
        'languageCode',
        'typeDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supplierTypeID' => 'integer',
        'languageCode' => 'string',
        'typeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supplierTypeID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'typeDescription' => 'required|string|max:255'
    ];

    /**
     * Get the supplier type that owns the translation.
     */
    public function supplierType()
    {
        return $this->belongsTo(SupplierType::class, 'supplierTypeID', 'supplierTypeID');
    }
}
