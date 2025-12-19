<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RegisteredSupplierContactDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="registeredSupplierID",
 *          description="registeredSupplierID",
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
class RegisteredSupplierContactDetail extends Model
{

    public $table = 'registeredsuppliercontactdetails';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'id';
    public $timestamps = false;


    public $fillable = [
        'registeredSupplierID',
        'contactTypeID',
        'contactPersonName',
        'contactPersonTelephone',
        'contactPersonFax',
        'contactPersonEmail',
        'isDefault'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'registeredSupplierID' => 'integer',
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

    public function contact_type()
    {
        return $this->belongsTo('App\Models\SupplierContactType','contactTypeID','supplierContactTypeID');
    }
    
}
