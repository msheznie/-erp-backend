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

    protected $appends = ['total'];


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



    public function getTotalAttribute()
    {
        $monthlySums =[];

        $monthlySums = array_fill(0, 13, ['total' => 0]);

        if($this->itemType === 2 && $this->isFinalLevel === 1)
        {
               foreach ($this->gl_codes as $glcode)
               {
                   $monthlySum = $glcode->items()->select('budjetAmtRpt','month')->where('companySystemID',1)->where('serviceLineSystemID', 1)->where('companyFinanceYearID',68)->where('budjetAmtRpt','>',0)->groupBy('month')->orderBy('month')->get();


                       foreach ($monthlySum as $month) {
                           if (!isset($monthlySums[$month->month-1])) {
                               $monthlySums[$month->month-1]['total'] = 0;
                           }
                           $monthlySums[$month->month-1]['total'] += $month->budjetAmtRpt;
                       }

               }

               // 13 th column as total
                $monthlySums[12]['total'] = (collect($monthlySums)->sum('total') == 0) ? 0 : collect($monthlySums)->sum('total') ;

        }

        return $monthlySums;

    }
}
