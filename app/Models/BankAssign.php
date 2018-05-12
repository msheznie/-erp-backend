<?php
/**
=============================================
-- File Name : BankAssign.php
-- Project Name : ERP
-- Module Name :  Bank Assigned
-- Author : Pasan Madhuranga
-- Create date : 21 - March 2018
-- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAssign
 * @package App\Models
 * @version March 21, 2018, 8:34 am UTC
 *
 * @property integer bankmasterAutoID
 * @property string companyID
 * @property string bankShortCode
 * @property string bankName
 * @property integer isAssigned
 * @property integer isDefault
 * @property integer isActive
 * @property string|\Carbon\Carbon createdDateTime
 * @property string createdByEmpID
 * @property string|\Carbon\Carbon TimeStamp
 */
class BankAssign extends Model
{
    //use SoftDeletes;

    public $table = 'erp_bankassigned';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'TimeStamp';
    protected $primaryKey  = 'bankAssignedAutoID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'bankmasterAutoID',
        'companyID',
        'bankShortCode',
        'bankName',
        'isAssigned',
        'isDefault',
        'isActive',
        'createdDateTime',
        'createdByEmpID',
        'TimeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bankAssignedAutoID' => 'integer',
        'bankmasterAutoID' => 'integer',
        'companyID' => 'string',
        'bankShortCode' => 'string',
        'bankName' => 'string',
        'isAssigned' => 'integer',
        'isDefault' => 'integer',
        'isActive' => 'integer',
        'createdByEmpID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company','companyID','CompanyID');
    }

    
}
