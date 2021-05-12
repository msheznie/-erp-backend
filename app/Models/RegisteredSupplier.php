<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RegisteredSupplier",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierName",
 *          description="supplierName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="telephone",
 *          description="telephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supEmail",
 *          description="supEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierCountryID",
 *          description="supplierCountryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="registrationExprity",
 *          description="registrationExprity",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="nameOnPaymentCheque",
 *          description="nameOnPaymentCheque",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address",
 *          description="address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fax",
 *          description="fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="webAddress",
 *          description="webAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="registrationNumber",
 *          description="registrationNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDate",
 *          description="createdDate",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class RegisteredSupplier extends Model
{

    public $table = 'registeredsupplier';
    
    const CREATED_AT = 'createdDate';
    const UPDATED_AT = null;




    public $fillable = [
        'supplierName',
        'telephone',
        'supEmail',
        'supplierCountryID',
        'registrationExprity',
        'currency',
        'nameOnPaymentCheque',
        'address',
        'fax',
        'webAddress',
        'registrationNumber',
        'approvedYN',
        'approvedEmpSystemID',
        'approvedby',
        'approvedDate',
        'approvedComment',
        'supplierConfirmedYN',
        'supplierConfirmedEmpID',
        'supplierConfirmedEmpSystemID',
        'supplierConfirmedDate',
        'supplierConfirmedEmpName',
        'supplierCodeSystem',
        'companySystemID',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'supplierCodeSystem' => 'integer',
        'supplierName' => 'string',
        'telephone' => 'string',
        'supEmail' => 'string',
        'supplierCountryID' => 'integer',
        'registrationExprity' => 'datetime',
        'currency' => 'integer',
        'nameOnPaymentCheque' => 'string',
        'address' => 'string',
        'fax' => 'string',
        'webAddress' => 'string',
        'registrationNumber' => 'string',
        'approvedYN' => 'integer',
        'approvedEmpSystemID' => 'integer',
        'approvedby' => 'string',
        'approvedDate' => 'datetime',
        'approvedComment' => 'string',
        'supplierConfirmedYN' => 'integer',
        'supplierConfirmedEmpID' => 'string',
        'supplierConfirmedEmpName' => 'string',
        'supplierConfirmedEmpSystemID' => 'integer',
        'supplierConfirmedDate' => 'datetime',
        'RollLevForApp_curr' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function supplier_currency(){
            return $this->hasMany('App\Models\RegisteredSupplierCurrency','registeredSupplierID','id');
    }

    public function supplier_contact_details(){
            return $this->hasMany('App\Models\RegisteredSupplierContactDetail','registeredSupplierID','id');
    }

     public function supplier_attachments(){
            return $this->hasMany('App\Models\RegisteredSupplierAttachment','resgisteredSupplierID','id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\CountryMaster','supplierCountryID','countryID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','id');
    }

     public function final_approved_by()
    {
        return $this->belongsTo('App\Models\Employee','approvedEmpSystemID','employeeSystemID');
    }
}
