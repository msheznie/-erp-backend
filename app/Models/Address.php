<?php
/**
 * =============================================
 * -- File Name : Address.php
 * -- Project Name : ERP
 * -- Module Name :  Address
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Address",
 *      required={""},
 *      @SWG\Property(
 *          property="addressID",
 *          description="addressID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="locationID",
 *          description="locationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="addressTypeID",
 *          description="addressTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addressDescrption",
 *          description="addressDescrption",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonID",
 *          description="contactPersonID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonTelephone",
 *          description="contactPersonTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonFaxNo",
 *          description="contactPersonFaxNo",
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
class Address extends Model
{

    public $table = 'erp_address';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'addressID';



    public $fillable = [
        'companySystemID',
        'companyID',
        'locationID',
        'departmentID',
        'addressTypeID',
        'addressDescrption',
        'contactPersonID',
        'contactPersonTelephone',
        'contactPersonFaxNo',
        'contactPersonEmail',
        'vat_number',
        'isDefault',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'addressID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'locationID' => 'integer',
        'departmentID' => 'string',
        'addressTypeID' => 'integer',
        'addressDescrption' => 'string',
        'contactPersonID' => 'string',
        'contactPersonTelephone' => 'string',
        'contactPersonFaxNo' => 'string',
        'contactPersonEmail' => 'string',
        'vat_number' => 'string',
        'isDefault' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function type(){
        return $this->belongsTo('App\Models\AddressType','addressTypeID','addressTypeID');
    }
    
}
