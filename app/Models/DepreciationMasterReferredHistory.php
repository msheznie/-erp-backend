<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DepreciationMasterReferredHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="depMasterReferredID",
 *          description="depMasterReferredID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depMasterAutoID",
 *          description="depMasterAutoID",
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
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depCode",
 *          description="depCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depMonthYear",
 *          description="depMonthYear",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depLocalCur",
 *          description="depLocalCur",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depAmountLocal",
 *          description="depAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="depRptCur",
 *          description="depRptCur",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depAmountRpt",
 *          description="depAmountRpt",
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
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="isDepProcessingYN",
 *          description="isDepProcessingYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpName",
 *          description="confirmedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      )
 * )
 */
class DepreciationMasterReferredHistory extends Model
{

    public $table = 'erp_fa_depmaster_referred_history';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'depMasterReferredID';

    public $fillable = [
        'depMasterAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'depCode',
        'depDate',
        'depMonthYear',
        'depLocalCur',
        'depAmountLocal',
        'depRptCur',
        'depAmountRpt',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'isDepProcessingYN',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'createdUserID',
        'createdUserSystemID',
        'createdPCID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'depMasterReferredID' => 'integer',
        'depMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'depCode' => 'string',
        'depMonthYear' => 'string',
        'depLocalCur' => 'integer',
        'depAmountLocal' => 'float',
        'depRptCur' => 'integer',
        'depAmountRpt' => 'float',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'boolean',
        'RollLevForApp_curr' => 'boolean',
        'isDepProcessingYN' => 'boolean',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByEmpName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdPCID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
