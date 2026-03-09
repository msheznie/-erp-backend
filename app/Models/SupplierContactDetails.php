<?php
/**
 * =============================================
 * -- File Name : SupplierContactDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Contact Details
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierContactDetails
 * @package App\Models
 * @version March 6, 2018, 10:52 am UTC
 *
 * @property integer supplierID
 * @property integer contactTypeID
 * @property string contactPersonName
 * @property string contactPersonTelephone
 * @property string contactPersonFax
 * @property string contactPersonEmail
 * @property integer isDefault
 * @property string|\Carbon\Carbon timestamp
 */
class SupplierContactDetails extends Model
{
   // use SoftDeletes;

    public $table = 'suppliercontactdetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'supplierContactID';



    public $fillable = [
        'supplierID',
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
        'supplierContactID' => 'integer',
        'supplierID' => 'integer',
        'contactTypeID' => 'integer',
        'contactPersonName' => 'string',
        'contactPersonTelephone' => 'string',
        'contactPersonFax' => 'string',
        'contactPersonEmail' => 'string',
        'isDefault' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
