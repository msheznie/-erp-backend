<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankMaster
 * @package App\Models
 * @version March 21, 2018, 5:24 am UTC
 *
 * @property string bankShortCode
 * @property string bankName
 * @property string|\Carbon\Carbon createdDateTime
 * @property string createdByEmpID
 * @property string|\Carbon\Carbon TimeStamp
 */
class BankMaster extends Model
{
    //use SoftDeletes;

    public $table = 'erp_bankmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';


    protected $dates = ['deleted_at'];
    protected $primaryKey  = 'bankmasterAutoID';


    public $fillable = [
        'bankShortCode',
        'bankName',
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
        'bankmasterAutoID' => 'integer',
        'bankShortCode' => 'string',
        'bankName' => 'string',
        'createdByEmpID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
