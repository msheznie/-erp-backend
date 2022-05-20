<?php

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;
use App\Models\ErpProjectMaster;
use App\Models\BudgetConsumedData;

/**
 * @SWG\Definition(
 *      definition="ProjectGlDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="ID",
 *          description="ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="projectID",
 *          description="projectID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
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
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updatedBy",
 *          description="updatedBy",
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
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ProjectGlDetail extends Model
{
    use Compoships;
    public $table = 'projectgldetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'ID';

    protected $appends = ['consumed_amount','consumed_amount_project'];

    public $fillable = [
        'projectID',
        'chartOfAccountSystemID',
        'companySystemID',
        'amount',
        'createdBy',
        'updatedBy'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ID' => 'integer',
        'projectID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'companySystemID' => 'integer',
        'amount' => 'float',
        'createdBy' => 'integer',
        'updatedBy' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function getConsumedAmountAttribute()
    {
        $consumedAmountRpt = BudgetConsumedData::where('projectID', $this->projectID)
                                            ->where('chartOfAccountID', $this->chartOfAccountSystemID)
                                            ->sum('consumedRptAmount');

        $projectMaster = ErpProjectMaster::with(['company'])->find($this->projectID);
        $consumedAmount = 0;
        if ($projectMaster) {
            $convertAmount = \Helper::currencyConversion($projectMaster->companySystemID, $projectMaster->company->reportingCurrency, $projectMaster->projectCurrencyID, $consumedAmountRpt);

            $consumedAmount = $convertAmount['documentAmount'];
        }

        return $consumedAmount;

    }

    public function getConsumedAmountProjectAttribute(){
        $consumedAmountRpt = BudgetConsumedData::where('projectID', $this->projectID)
            ->sum('consumedRptAmount');

        $projectMaster = ErpProjectMaster::with(['company'])->find($this->projectID);
        $consumedAmount = 0;
        if ($projectMaster) {
            $convertAmount = \Helper::currencyConversion($projectMaster->companySystemID, $projectMaster->company->reportingCurrency, $projectMaster->projectCurrencyID, $consumedAmountRpt);

            $consumedAmount = $convertAmount['documentAmount'];
        }

        return $consumedAmount;
    }




    public function chartofaccounts(){
        return $this->belongsTo('App\Models\ChartOfAccountsAssigned','chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function consumed_data()
    {
        return $this->belongsTo('App\Models\BudgetConsumedData', ['projectID', 'chartOfAccountSystemID'], ['projectID', 'chartOfAccountID']);
    }
}
