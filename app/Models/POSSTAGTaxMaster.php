<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSTAGTaxMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="effectiveFrom",
 *          description="date tax effective from",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="erp_tax_master_id",
 *          description="erp_tax_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputVatGLAccountAutoID",
 *          description="inputVat and WHT expense",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputVatTransferGLAccountAutoID",
 *          description="inputVatTransferGLAccountAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="isClaimable",
 *          description="0- no 1-yes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="outputVatGLAccountAutoID",
 *          description="outputVatGLAccountAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="outputVatTransferGLAccountAutoID",
 *          description="outputVatTransferGLAccountAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierGLAutoID",
 *          description="supplierGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxCategory",
 *          description="1 -> Other, 2->VAT,3->WHT",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxDescription",
 *          description="taxDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxMasterAutoID",
 *          description="taxMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxPercentage",
 *          description="taxPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxReferenceNo",
 *          description="taxReferenceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxShortCode",
 *          description="taxShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxType",
 *          description="sales tax -1 purchase tax -2",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSTAGTaxMaster extends Model
{

    public $table = 'pos_stag_taxmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'effectiveFrom',
        'erp_tax_master_id',
        'inputVatGLAccountAutoID',
        'inputVatTransferGLAccountAutoID',
        'isActive',
        'isClaimable',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'outputVatGLAccountAutoID',
        'outputVatTransferGLAccountAutoID',
        'supplierGLAutoID',
        'taxCategory',
        'taxDescription',
        'taxPercentage',
        'taxReferenceNo',
        'taxShortCode',
        'taxType',
        'timestamp',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyCode' => 'string',
        'companyID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPCID' => 'string',
        'createdUserGroup' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'effectiveFrom' => 'date',
        'erp_tax_master_id' => 'integer',
        'inputVatGLAccountAutoID' => 'integer',
        'inputVatTransferGLAccountAutoID' => 'integer',
        'isActive' => 'boolean',
        'isClaimable' => 'integer',
        'modifiedDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'outputVatGLAccountAutoID' => 'integer',
        'outputVatTransferGLAccountAutoID' => 'integer',
        'supplierGLAutoID' => 'integer',
        'taxCategory' => 'integer',
        'taxDescription' => 'string',
        'taxMasterAutoID' => 'integer',
        'taxPercentage' => 'float',
        'taxReferenceNo' => 'string',
        'taxShortCode' => 'string',
        'taxType' => 'boolean',
        'timestamp' => 'datetime',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'taxDescription' => 'required',
        'taxShortCode' => 'required'
    ];

    
}
