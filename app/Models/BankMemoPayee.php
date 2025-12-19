<?php
/**
=============================================
-- File Name : BankMemoPayee.php
-- Project Name : ERP
-- Module Name :  Bank Memo Payee
-- Author : Fayas
-- Create date : 26 - November 2018
-- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BankMemoPayee",
 *      required={""},
 *      @SWG\Property(
 *          property="bankMemoPayeeID",
 *          description="bankMemoPayeeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankMemoTypeID",
 *          description="bankMemoTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="memoHeader",
 *          description="memoHeader",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="memoDetail",
 *          description="memoDetail",
 *          type="string"
 *      )
 * )
 */
class BankMemoPayee extends Model
{

    public $table = 'erp_bankmemopayee';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'bankMemoPayeeID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'bankMemoTypeID',
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
        'bankMemoPayeeID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'bankMemoTypeID' => 'integer',
        'memoHeader' => 'string',
        'memoDetail' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function scopeOrderBySort($q){

        return $q->join('erp_bankmemotypes', 'erp_bankmemopayee.bankMemoTypeID', '=', 'erp_bankmemotypes.bankMemoTypeID')
            ->addSelect('erp_bankmemopayee.*', 'erp_bankmemotypes.sortOrder')
            ->orderBy('erp_bankmemotypes.sortOrder', 'asc');
    }
}
