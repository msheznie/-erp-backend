<?php
/**
 * =============================================
 * -- File Name : ChequeRegisterDetail.php
 * -- Project Name : ERP
 * -- Module Name : Treasury Management
 * -- Author : Mohamed Rilwan
 * -- Create date : 19- September 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;
use Awobaz\Compoships\Compoships;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ChequeRegisterDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cheque_register_master_id",
 *          description="cheque_register_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cheque_no",
 *          description="cheque_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_pc",
 *          description="created_pc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_pc",
 *          description="updated_pc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_id",
 *          description="document_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_master_id",
 *          description="document_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="Default - 0 - unused, 1 - used, 2- cancel ",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ChequeRegisterDetail extends Model
{
    use Compoships;
    public $table = 'erp_cheque_register_detail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    public $fillable = [
        'cheque_register_master_id',
        'cheque_no',
        'description',
        'created_by',
        'created_pc',
        'updated_by',
        'updated_pc',
        'isPrinted',
        'cheque_printed_at',
        'cheque_print_by',
        'company_id',
        'document_id',
        'document_master_id',
        'cancel_narration',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cheque_register_master_id' => 'integer',
        'cheque_no' => 'string',
        'description' => 'string',
        'created_by' => 'integer',
        'created_pc' => 'string',
        'updated_by' => 'integer',
        'updated_pc' => 'string',
        'isPrinted' => 'integer',
        'cheque_printed_at' => 'datetime',
        'cheque_print_by' => 'integer',
        'company_id' => 'integer',
        'document_id' => 'integer',
        'document_master_id' => 'integer',
        'cancel_narration' => 'string',
        'status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'id' => 'required',
//        'cheque_register_master_id' => 'required'
    ];

    public function document()
    {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'document_id', 'PayMasterAutoId');
    }

    public function pdc_printed_history()
    {
        return $this->hasMany('App\Models\PdcLogPrintedHistory', 'chequeNo', 'cheque_no');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\ChequeRegister', 'cheque_register_master_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\Employee', 'updated_by', 'employeeSystemID');
    }

    public function printBy()
    {
        return $this->belongsTo('App\Models\Employee', 'updated_by', 'employeeSystemID');
    }

    public function latestChequeUpdateReason()
    {
        return $this->hasOne('App\Models\ChequeUpdateReason', 'document_id', 'document_id')->latest();
    }    


}
