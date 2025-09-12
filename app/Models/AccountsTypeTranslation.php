<?php
/**
 * =============================================
 * -- File Name : AccountsTypeTranslation.php
 * -- Project Name : ERP
 * -- Module Name : Accounts Type Translation
 * -- Author : System
 * -- Create date : 12 - September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AccountsTypeTranslation",
 *      required={"accountsType", "languageCode", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountsType",
 *          description="accountsType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      )
 * )
 */
class AccountsTypeTranslation extends Model
{
    public $table = 'accountstype_translation';
    
    public $timestamps = true;

    public $fillable = [
        'accountsType',
        'languageCode',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'accountsType' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'accountsType' => 'required|integer|exists:accountstype,accountsType',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the accounts type that owns the translation.
     */
    public function accountsType()
    {
        return $this->belongsTo(AccountsType::class, 'accountsType', 'accountsType');
    }
}
