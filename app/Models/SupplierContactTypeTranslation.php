<?php
/**
 * =============================================
 * -- File Name : SupplierContactTypeTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Contact Type Translation
 * -- Author : System Generated
 * -- Create date : 13- September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierContactTypeTranslation
 * @package App\Models
 * @version September 13, 2025, 2:00 pm UTC
 *
 * @property integer supplierContactTypeID
 * @property string languageCode
 * @property string supplierContactDescription
 */
class SupplierContactTypeTranslation extends Model
{
    //use SoftDeletes;

    public $table = 'suppliercontacttype_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'supplierContactTypeID',
        'languageCode',
        'supplierContactDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supplierContactTypeID' => 'integer',
        'languageCode' => 'string',
        'supplierContactDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supplierContactTypeID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'supplierContactDescription' => 'required|string|max:255'
    ];

    /**
     * Get the supplier contact type that owns the translation.
     */
    public function supplierContactType()
    {
        return $this->belongsTo(SupplierContactType::class, 'supplierContactTypeID', 'supplierContactTypeID');
    }
}
