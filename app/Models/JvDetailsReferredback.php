<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="JvDetailsReferredback",
 *      required={""},
 *      @SWG\Property(
 *          property="jvDetailReferredBackID",
 *          description="jvDetailReferredBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jvDetailAutoID",
 *          description="jvDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jvMasterAutoId",
 *          description="jvMasterAutoId",
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
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="recurringjvMasterAutoId",
 *          description="recurringjvMasterAutoId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="recurringjvDetailAutoID",
 *          description="recurringjvDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="recurringMonth",
 *          description="recurringMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
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
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glAccount",
 *          description="glAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glAccountDescription",
 *          description="glAccountDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="referenceGLCode",
 *          description="referenceGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="referenceGLDescription",
 *          description="referenceGLDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientContractID",
 *          description="clientContractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyER",
 *          description="currencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="debitAmount",
 *          description="debitAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditAmount",
 *          description="creditAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyIDForConsole",
 *          description="companyIDForConsole",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="selectedForConsole",
 *          description="selectedForConsole",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      )
 * )
 */
class JvDetailsReferredback extends Model
{

    public $table = 'erp_jvdetailreferredback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'jvDetailReferredBackID';

    public $fillable = [
        'jvDetailAutoID',
        'jvMasterAutoId',
        'documentSystemID',
        'documentID',
        'recurringjvMasterAutoId',
        'recurringjvDetailAutoID',
        'recurringMonth',
        'serviceLineSystemID',
        'serviceLineCode',
        'companySystemID',
        'companyID',
        'chartOfAccountSystemID',
        'glAccount',
        'glAccountDescription',
        'referenceGLCode',
        'referenceGLDescription',
        'comments',
        'contractUID',
        'clientContractID',
        'currencyID',
        'currencyER',
        'debitAmount',
        'creditAmount',
        'timesReferred',
        'companyIDForConsole',
        'selectedForConsole',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timeStamp',
        'detail_project_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'jvDetailReferredBackID' => 'integer',
        'jvDetailAutoID' => 'integer',
        'jvMasterAutoId' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'recurringjvMasterAutoId' => 'integer',
        'recurringjvDetailAutoID' => 'integer',
        'recurringMonth' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'glAccount' => 'string',
        'glAccountDescription' => 'string',
        'referenceGLCode' => 'string',
        'referenceGLDescription' => 'string',
        'comments' => 'string',
        'contractUID' => 'integer',
        'clientContractID' => 'string',
        'currencyID' => 'integer',
        'currencyER' => 'float',
        'debitAmount' => 'float',
        'creditAmount' => 'float',
        'timesReferred' => 'integer',
        'companyIDForConsole' => 'string',
        'selectedForConsole' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'detail_project_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currencyID', 'currencyID');
    }

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function contract()
    {
        return $this->belongsTo('App\Models\Contract', 'contractUID', 'clientContractID');
    }
    
}
