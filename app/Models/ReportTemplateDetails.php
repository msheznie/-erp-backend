<?php
/**
 * =============================================
 * -- File Name : ReportTemplateDetails.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 20- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\Request;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="detID",
 *          description="detID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportTemplateID",
 *          description="companyReportTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemType",
 *          description="itemType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterID",
 *          description="masterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountType",
 *          description="accountType",
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
 *          property="createdPCID",
 *          description="createdPCID",
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
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      )
 * )
 */
class ReportTemplateDetails extends Model
{

    public $table = 'erp_companyreporttemplatedetails';

    protected $appends = ['total','extend','glData'];


    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'detID';

    public $fillable = [
        'companyReportTemplateID',
        'description',
        'itemType',
        'sortOrder',
        'masterID',
        'isFinalLevel',
        'accountType',
        'controlAccountType',
        'categoryType',
        'fontColor',
        'bgColor',
        'netProfitStatus',
        'hideHeader',
        'companySystemID',
        'companyID',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'prefix',
        'serialLength',
        'lastSerialNo',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'detID' => 'integer',
        'companyReportTemplateID' => 'integer',
        'description' => 'string',
        'itemType' => 'integer',
        'sortOrder' => 'integer',
        'masterID' => 'integer',
        'isFinalLevel' => 'integer',
        'controlAccountType' => 'integer',
        'accountType' => 'string',
        'prefix' => 'string',
        'serialLength' => 'integer',
        'lastSerialNo' => 'integer',
        'categoryType' => 'integer',
        'netProfitStatus' => 'integer',
        'fontColor' => 'string',
        'bgColor' => 'string',
        'hideHeader' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfMaster($query, $companyReportTemplateID)
    {
        return $query->where('companyReportTemplateID',  $companyReportTemplateID);
    }

    public function subcategory()
    {
        return $this->hasMany(ReportTemplateDetails::class,'masterID','detID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\ReportTemplate','companyReportTemplateID','companyReportTemplateID');
    }

    public function gllink()
    {
        return $this->hasMany('App\Models\ReportTemplateLinks','templateDetailID','detID');
    }

    public function subcatlink()
    {
        return $this->hasMany('App\Models\ReportTemplateLinks','subCategory','detID');
    }

    public function subcategorytot()
    {
        return $this->hasMany('App\Models\ReportTemplateLinks','templateDetailID','detID');
    }

    public function gl_codes()
    {
        return $this->gllink();
    }


    public function getGlDataAttribute()
    {
        $inputData = Request::all();
        if(isset($inputData['updatedCategory']['masterID']) && $inputData['updatedCategory']['detID'] == $this->detID)
        {
            return $inputData['updatedCategory']['glData'];
        }
    }

    public function getExtendAttribute()
    {
        $inputData = Request::all();

        if(isset($inputData['updatedCategory']['masterID']) && $inputData['updatedCategory']['detID'] == $this->detID)
        {
            return true;
        }else {
            return false;
        }



    }

    public function getTotalAttribute()
    {
        $monthlySums =[];

        $inputData = Request::all();

        if(isset($inputData['id']))
        {
            $budgetMaster = BudgetMaster::find($inputData['id']);
        }

        $monthlySums = array_fill(0, 13, ['total' => 0]);

        if($this->itemType === 2 && $this->isFinalLevel === 1 && isset($budgetMaster))
        {
            foreach ($this->gl_codes as $glcode)
            {
                $monthlySum = $glcode->items()->select('budjetAmtRpt','month')->where('companySystemID',$budgetMaster['companySystemID'])->where('serviceLineSystemID', $budgetMaster['serviceLineSystemID'])->where('companyFinanceYearID',$budgetMaster['companyFinanceYearID'])->where('budgetmasterID',$budgetMaster['budgetmasterID'])->groupBy('month')->orderBy('month')->get();


                foreach ($monthlySum as $key => $month) {
                    if (!isset($monthlySums[$month->month-1])) {
                        $monthlySums[$month->month-1]['total'] = 0;
                    }

                    $monthlySums[$month->month-1]['total'] += $month->budjetAmtRpt;

                    if($key == 12)
                    {
                        $monthlySums[12]['total'] = (!collect($monthlySums)->sum('total')) ? 0 : collect($monthlySums)->sum('total') ;

                    }


                }

            }


        }


        if($this->itemType === 3 && $this->isFinalLevel === 1 & isset($budgetMaster) )
        {
            $monthlySums = $this->calculateGLTotalAmounts($budgetMaster,$monthlySums);
        }


        if($this->itemType === 3 && $this->isFinalLevel === 0 & isset($budgetMaster) && is_null($this->masterID)) {
            $monthlySums = $this->calculateGLTotalAmounts($budgetMaster,$monthlySums);
        }


        $monthlySums[12]['total'] = (!collect($monthlySums)->sum('total')) ? 0 : collect($monthlySums)->sum('total') ;


        return $monthlySums;

    }


    public function calculateGLTotalAmounts($budgetMaster, $monthlySums)
    {
        $processGLCodes = function ($glCodes, $monthlySums) use ($budgetMaster, &$processGLCodes) {
            foreach ($glCodes as $glCode) {
                $monthlySum = $glCode->items()
                    ->select('budjetAmtRpt', 'month')
                    ->where('companySystemID', $budgetMaster['companySystemID'])
                    ->where('serviceLineSystemID', $budgetMaster['serviceLineSystemID'])
                    ->where('companyFinanceYearID', $budgetMaster['companyFinanceYearID'])
                    ->where('budgetmasterID', $budgetMaster['budgetmasterID'])
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();

                foreach ($monthlySum as $month) {
                    $monthIndex = $month->month - 1;
                    $monthlySums[$monthIndex]['total'] = ($monthlySums[$monthIndex]['total'] ?? 0) + $month->budjetAmtRpt;
                }
            }

            return $monthlySums;
        };

        $traverseCategories = function ($categories, $monthlySums) use ($processGLCodes, &$traverseCategories) {
            foreach ($categories as $category) {
                $glCodes = $category->gl_codes;

                if (!is_null($glCodes)) {
                    $monthlySums = $processGLCodes($glCodes, $monthlySums);
                } else {
                    $subCategories = $category->subCategory()->get();
                    if (!is_null($subCategories)) {
                        $monthlySums = $traverseCategories($subCategories, $monthlySums);
                    }
                }
            }

            return $monthlySums;
        };

        $monthlySums = $traverseCategories($this->gl_codes, $monthlySums);

        return $monthlySums;
    }


}
