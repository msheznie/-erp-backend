<?php
/**
 * =============================================
 * -- File Name : BankMemoSupplierMaster.php
 * -- Project Name : ERP
 * -- Module Name : Bank Memo Supplier Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankMemoSupplierMaster
 * @package App\Models
 * @version March 8, 2018, 5:51 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property string memoHeader
 * @property string memoDetail
 * @property string|\Carbon\Carbon timestamp
 */
class BankMemoSupplierMaster extends Model
{
    //use SoftDeletes;

    public $table = 'erp_bankmemosuppliermaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companySystemID',
        'companyID',
        'memoHeader',
        'memoDetail',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'memoHeader' => 'string',
        'memoDetail' => 'string',
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
