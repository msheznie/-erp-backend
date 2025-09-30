<?php
/**
 * =============================================
 * -- File Name : AddressTypeTranslation.php
 * -- Project Name : ERP
 * -- Module Name : Address Type Translation
 * -- Author : System
 * -- Create date : 12 - September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AddressTypeTranslation",
 *      required={"addressTypeID", "languageCode", "addressTypeDescription"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addressTypeID",
 *          description="addressTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="addressTypeDescription",
 *          description="addressTypeDescription",
 *          type="string"
 *      )
 * )
 */
class AddressTypeTranslation extends Model
{
    public $table = 'erp_addresstype_translation';
    
    public $timestamps = true;

    public $fillable = [
        'addressTypeID',
        'languageCode',
        'addressTypeDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'addressTypeID' => 'integer',
        'languageCode' => 'string',
        'addressTypeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'addressTypeID' => 'required|integer|exists:erp_addresstype,addressTypeID',
        'languageCode' => 'required|string|max:10',
        'addressTypeDescription' => 'required|string|max:255'
    ];

    /**
     * Get the address type that owns the translation.
     */
    public function addressType()
    {
        return $this->belongsTo(AddressType::class, 'addressTypeID', 'addressTypeID');
    }
}
