<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSTAGTaxLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
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
 *          property="countryID",
 *          description="countryID",
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
 *          property="documentDetailAutoID",
 *          description="IF line by tax documentdetailTable AutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="short code of document Ex : Customer Invocie - CINV",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentMasterAutoID",
 *          description="documentMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="formula",
 *          description="formula",
 *          type="string"
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
 *          property="ismanuallychanged",
 *          description="ismanuallychanged",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSync",
 *          description="0 => Not Synced 
1 => Send to ERP 
2 => Fully Synced",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="locationID",
 *          description="locationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="locationType",
 *          description="locationType",
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
 *          property="outputVatGL",
 *          description="outputVatGL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="outputVatTransferGL",
 *          description="outputVatTransferGL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="partyID",
 *          description="partyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="partyVATEligibleYN",
 *          description="0 => No, 1 => Yes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxDetailAutoID",
 *          description="if Common Tax taxdetailtable AutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxFormulaDetailID",
 *          description="taxFormulaDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxFormulaMasterID",
 *          description="taxFormulaMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxGlAutoID",
 *          description="taxGlAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxLedgerAutoID",
 *          description="taxLedgerAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxMasterID",
 *          description="taxMasterID",
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
 *      ),
 *      @SWG\Property(
 *          property="transferGLAutoID",
 *          description="transferGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatTypeID",
 *          description="vatTypeID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSTAGTaxLedger extends Model
{

    public $table = 'pos_stag_taxledger';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'amount',
        'companyCode',
        'companyID',
        'countryID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'documentDetailAutoID',
        'documentID',
        'documentMasterAutoID',
        'formula',
        'isClaimable',
        'isClaimed',
        'ismanuallychanged',
        'isSync',
        'locationID',
        'locationType',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'outputVatGL',
        'outputVatTransferGL',
        'partyID',
        'partyVATEligibleYN',
        'taxDetailAutoID',
        'taxFormulaDetailID',
        'taxFormulaMasterID',
        'taxGlAutoID',
        'taxMasterID',
        'taxPercentage',
        'timestamp',
        'transaction_log_id',
        'transferGLAutoID',
        'vatTypeID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'companyCode' => 'string',
        'companyID' => 'integer',
        'countryID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPCID' => 'string',
        'createdUserGroup' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'documentDetailAutoID' => 'integer',
        'documentID' => 'string',
        'documentMasterAutoID' => 'integer',
        'formula' => 'string',
        'isClaimable' => 'integer',
        'isClaimed' => 'integer',
        'ismanuallychanged' => 'integer',
        'isSync' => 'integer',
        'locationID' => 'integer',
        'locationType' => 'integer',
        'modifiedDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'outputVatGL' => 'string',
        'outputVatTransferGL' => 'string',
        'partyID' => 'integer',
        'partyVATEligibleYN' => 'integer',
        'taxDetailAutoID' => 'integer',
        'taxFormulaDetailID' => 'integer',
        'taxFormulaMasterID' => 'integer',
        'taxGlAutoID' => 'integer',
        'taxLedgerAutoID' => 'integer',
        'taxMasterID' => 'integer',
        'taxPercentage' => 'float',
        'timestamp' => 'datetime',
        'transaction_log_id' => 'integer',
        'transferGLAutoID' => 'integer',
        'vatTypeID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'isSync' => 'required'
    ];

    
}
