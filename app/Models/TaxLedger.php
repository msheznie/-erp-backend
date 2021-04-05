<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TaxLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="taxLedgerID",
 *          description="taxLedgerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentMasterAutoID",
 *          description="documentMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentDate",
 *          description="documentDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="subCategoryID",
 *          description="subCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterCategoryID",
 *          description="masterCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rcmApplicableYN",
 *          description="rcmApplicableYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transAmount",
 *          description="transAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transER",
 *          description="transER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localER",
 *          description="localER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comRptER",
 *          description="comRptER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyID",
 *          description="rptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transCurrencyID",
 *          description="transCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClaimable",
 *          description="isClaimable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClaimed",
 *          description="isClaimed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxAuthorityAutoID",
 *          description="taxAuthorityAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputVATGlAccountID",
 *          description="inputVATGlAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputVatTransferAccountID",
 *          description="inputVatTransferAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="outputVatTransferGLAccountID",
 *          description="outputVatTransferGLAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="outputVatGLAccountID",
 *          description="outputVatGLAccountID",
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
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
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
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TaxLedger extends Model
{

    public $table = 'erp_tax_ledger';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'taxLedgerID';


    public $fillable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentCode',
        'documentDate',
        'subCategoryID',
        'masterCategoryID',
        'rcmApplicableYN',
        'localAmount',
        'rptAmount',
        'transAmount',
        'transER',
        'localER',
        'comRptER',
        'localCurrencyID',
        'rptCurrencyID',
        'transCurrencyID',
        'isClaimable',
        'isClaimed',
        'taxAuthorityAutoID',
        'inputVATGlAccountID',
        'inputVatTransferAccountID',
        'outputVatTransferGLAccountID',
        'outputVatGLAccountID',
        'companySystemID',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxLedgerID' => 'integer',
        'documentSystemID' => 'integer',
        'documentMasterAutoID' => 'integer',
        'documentCode' => 'string',
        'documentDate' => 'datetime',
        'subCategoryID' => 'integer',
        'masterCategoryID' => 'integer',
        'rcmApplicableYN' => 'integer',
        'localAmount' => 'float',
        'rptAmount' => 'float',
        'transAmount' => 'float',
        'transER' => 'float',
        'localER' => 'float',
        'comRptER' => 'float',
        'localCurrencyID' => 'integer',
        'rptCurrencyID' => 'integer',
        'transCurrencyID' => 'integer',
        'isClaimable' => 'integer',
        'isClaimed' => 'integer',
        'taxAuthorityAutoID' => 'integer',
        'inputVATGlAccountID' => 'integer',
        'inputVatTransferAccountID' => 'integer',
        'outputVatTransferGLAccountID' => 'integer',
        'outputVatGLAccountID' => 'integer',
        'companySystemID' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
