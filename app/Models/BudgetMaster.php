<?php
/**
 * =============================================
 * -- File Name : BudgetMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Fayas
 * -- Create date : 16 - October 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BudgetMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetmasterID",
 *          description="budgetmasterID",
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
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
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
 *          property="templateMasterID",
 *          description="templateMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Year",
 *          description="Year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="month",
 *          description="month",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserSystemID",
 *          description="createdByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserID",
 *          description="createdByUserID",
 *          type="string"
 *      )
 * )
 */
class BudgetMaster extends Model
{

    public $table = 'erp_budgetmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'budgetmasterID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLineCode',
        'templateMasterID',
        'Year',
        'month',
        'createdByUserSystemID',
        'createdByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetmasterID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'companyFinanceYearID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'templateMasterID' => 'integer',
        'Year' => 'integer',
        'month' => 'integer',
        'createdByUserSystemID' => 'integer',
        'createdByUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function template_master()
    {
        return $this->belongsTo('App\Models\TemplatesMaster', 'templateMasterID', 'templatesMasterAutoID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

}
