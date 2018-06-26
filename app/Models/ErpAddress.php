<?php
/**
 * =============================================
 * -- File Name : ErpAddress.php
 * -- Project Name : ERP
 * -- Module Name :  Erp Address
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ErpAddress
 * @package App\Models
 * @version April 9, 2018, 2:58 pm UTC
 *
 * @property string companyID
 * @property integer locationID
 * @property string departmentID
 * @property integer addressTypeID
 * @property string addressDescrption
 * @property string contactPersonID
 * @property string contactPersonTelephone
 * @property string contactPersonFaxNo
 * @property string contactPersonEmail
 * @property integer isDefault
 * @property string|\Carbon\Carbon timeStamp
 */
class ErpAddress extends Model
{
    //use SoftDeletes;

    public $table = 'erp_address';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'addressID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'companyID',
        'locationID',
        'departmentID',
        'addressTypeID',
        'addressDescrption',
        'contactPersonID',
        'contactPersonTelephone',
        'contactPersonFaxNo',
        'contactPersonEmail',
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
        'companyID' => 'string',
        'locationID' => 'integer',
        'departmentID' => 'string',
        'addressTypeID' => 'integer',
        'addressDescrption' => 'string',
        'contactPersonID' => 'string',
        'contactPersonTelephone' => 'string',
        'contactPersonFaxNo' => 'string',
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
