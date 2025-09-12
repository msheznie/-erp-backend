<?php
/**
 * =============================================
 * -- File Name : ControlAccountTranslation.php
 * -- Project Name : ERP
 * -- Module Name : Control Account Translation
 * -- Author : System
 * -- Create date : 12 - September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ControlAccountTranslation",
 *      required={"controlAccountsSystemID", "languageCode", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="controlAccountsSystemID",
 *          description="controlAccountsSystemID",
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
class ControlAccountTranslation extends Model
{
    public $table = 'controlaccounts_translation';
    
    public $timestamps = true;

    public $fillable = [
        'controlAccountsSystemID',
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
        'controlAccountsSystemID' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'controlAccountsSystemID' => 'required|integer|exists:controlaccounts,controlAccountsSystemID',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the control account that owns the translation.
     */
    public function controlAccount()
    {
        return $this->belongsTo(ControlAccount::class, 'controlAccountsSystemID', 'controlAccountsSystemID');
    }
}

