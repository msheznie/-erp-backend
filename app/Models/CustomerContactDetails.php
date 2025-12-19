<?php
/**
 * =============================================
 * -- File Name : CustomerContactDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Contact Details
 * -- Author : Mohamed Fayas
 * -- Create date : 25- April 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerContactDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="customerContactID",
 *          description="customerContactID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contactTypeID",
 *          description="contactTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonName",
 *          description="contactPersonName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonTelephone",
 *          description="contactPersonTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonFax",
 *          description="contactPersonFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonEmail",
 *          description="contactPersonEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CustomerContactDetails extends Model
{

    public $table = 'customercontactdetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'customerContactID';


    public $fillable = [
        'customerID',
        'contactTypeID',
        'contactPersonName',
        'contactPersonTelephone',
        'contactPersonFax',
        'contactPersonEmail',
        'isDefault',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerContactID' => 'integer',
        'customerID' => 'integer',
        'contactTypeID' => 'integer',
        'contactPersonName' => 'string',
        'contactPersonTelephone' => 'string',
        'contactPersonFax' => 'string',
        'contactPersonEmail' => 'string',
        'isDefault' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
