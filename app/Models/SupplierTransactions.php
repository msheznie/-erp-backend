<?php
/**
 * =============================================
 * -- File Name : SupplierMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierMaster
 * @package App\Models
 * @version March 3, 2022, 11:27 am UTC
 *
 * @property string uniqueTextcode

 */
class SupplierTransactions extends Model
{
    //use SoftDeletes;

    public $table = 'erp_suppliertransactions';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey  = 'id';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'documentNarration',
        'supplierID',
        'supplierCode',
        'supplierName',
        'confirmedDate',
        'confirmedBy',
        'approvedDate',
        'lastApprovedBy',
        'transactionCurrency',
        'amount'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'string',
        'documentCode' => 'string',
        'documentNarration' => 'string',
        'supplierID' => 'integer',
        'supplierCode' => 'string',
        'supplierName' => 'string',
        'confirmedBy' => 'string',
        'lastApprovedBy' => 'string',
        'transactionCurrency' => 'string',
        'amount' => 'float'

    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
