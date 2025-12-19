<?php
/**
 * =============================================
 * -- File Name : ChequeRegister.php
 * -- Project Name : ERP
 * -- Module Name : Treasury Management
 * -- Author : Mohamed Rilwan
 * -- Create date : 19- September 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ChequeRegister",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="master_description",
 *          description="master_description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bank_id",
 *          description="bank_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bank_account_id",
 *          description="bank_account_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="no_of_cheques",
 *          description="no_of_cheques",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="started_cheque_no",
 *          description="started_cheque_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ended_cheque_no",
 *          description="ended_cheque_no",
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
 *      )
 * )
 */
class ChequeRegister extends Model
{

    public $table = 'erp_cheque_register_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    public $fillable = [
        'description',
        'bank_id',
        'bank_account_id',
        'no_of_cheques',
        'started_cheque_no',
        'ended_cheque_no',
        'company_id',
        'document_id',
        'created_by',
        'created_pc',
        'isActive',
        'updated_by',
        'updated_pc',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'bank_id' => 'integer',
        'bank_account_id' => 'integer',
        'no_of_cheques' => 'integer',
        'started_cheque_no' => 'string',
        'ended_cheque_no' => 'string',
        'company_id' => 'integer',
        'document_id' => 'integer',
        'created_by' => 'integer',
        'isActive' => 'integer',
        'created_pc' => 'string',
        'updated_by' => 'integer',
        'updated_pc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'id' => 'required'
    ];

    public function details()
    {
        return $this->hasMany('App\Models\ChequeRegisterDetail', 'cheque_register_master_id','id');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\BankMaster', 'bank_id', 'bankmasterAutoID');
    }

    public function bank_account()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bank_account_id', 'bankAccountAutoID');
    }
}
