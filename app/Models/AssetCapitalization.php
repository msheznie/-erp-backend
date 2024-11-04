<?php
/**
 * =============================================
 * -- File Name : AssetCapitalization.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mubashir
 * -- Create date : 26 - September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetCapitalization",
 *      required={""},
 *      @SWG\Property(
 *          property="capitalizationID",
 *          description="capitalizationID",
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
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
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
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="allocationTypeID",
 *          description="allocationTypeID",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="faCatID",
 *          description="faCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faID",
 *          description="faID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetNBVLocal",
 *          description="assetNBVLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="assetNBVRpt",
 *          description="assetNBVRpt",
 *          type="number",
 *          format="float"
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
 *          property="confirmedByName",
 *          description="confirmedByName",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class AssetCapitalization extends Model
{

    public $table = 'erp_fa_assetcapitalization';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'capitalizationID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'capitalizationCode',
        'documentDate',
        'companyFinanceYearID',
        'serialNo',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'narration',
        'allocationTypeID',
        'faCatID',
        'faID',
        'contraAccountSystemID',
        'contraAccountGLCode',
        'assetNBVLocal',
        'assetNBVRpt',
        'timesReferred',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'refferedBackYN',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'cancelledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'RollLevForApp_curr',
        'timestamp',
         'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'capitalizationID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'capitalizationCode' => 'string',
        'companyFinanceYearID' => 'integer',
        'serialNo' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'narration' => 'string',
        'allocationTypeID' => 'boolean',
        'faCatID' => 'integer',
        'faID' => 'integer',
        'contraAccountSystemID' => 'integer',
        'contraAccountGLCode' => 'string',
        'assetNBVLocal' => 'float',
        'assetNBVRpt' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'timesReferred ' => 'integer',
        'refferedBackYN ' => 'integer',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'cancelYN' => 'integer',
        'cancelComment' => 'string',
        'cancelledByEmpSystemID' => 'integer',
        'canceledByEmpID' => 'string',
        'canceledByEmpName' => 'string',
        'RollLevForApp_curr' => 'integer',
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfAsset($query, $faID)
    {
        return $query->where('faID',  $faID);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\AssetCapitalizationDetail', 'capitalizationID', 'capitalizationID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'capitalizationID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }


    public function financeperiod_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function financeyear_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function contra_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'contraAccountSystemID', 'chartOfAccountSystemID');
    }

    public function asset_by()
    {
        return $this->belongsTo('App\Models\FixedAssetMaster', 'faID', 'faID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'capitalizationID')->where('documentSystemID',63);
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_fa_assetcapitalization.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_fa_assetcapitalization.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeCharAccJoin($q,$as = 'chartofaccounts', $column = 'companySystemID' , $columnAs = 'AccountDescription')
    {
        return $q->leftJoin('chartofaccounts as '.$as,$as.'.chartOfAccountSystemID','erp_fa_assetcapitalization.'.$column)
        ->addSelect($as.".AccountDescription as ".$columnAs);
    }

    
    public function scopeAssetCatJoin($q,$as = 'erp_fa_category', $column = 'companySystemID' , $columnAs = 'catDescription')
    {
        return $q->leftJoin('erp_fa_category as '.$as,$as.'.faCatID','erp_fa_assetcapitalization.'.$column)
        ->addSelect($as.".catDescription as ".$columnAs);
    }

        
    public function scopeAssetJoin($q,$as = 'erp_fa_asset_master', $column = 'companySystemID' , $columnAs = 'assetDescription')
    {
        return $q->leftJoin('erp_fa_asset_master as '.$as,$as.'.faID','erp_fa_assetcapitalization.'.$column)
        ->addSelect($as.".assetDescription as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_fa_assetcapitalization_detail','erp_fa_assetcapitalization_detail.capitalizationID','erp_fa_assetcapitalization.capitalizationID');
    }


}
