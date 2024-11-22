<?php
/**
 * =============================================
 * -- File Name : CompanyFinanceYear.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Year
 * -- Author : Mohamed Nazir
 * -- Create date : 12 - June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="CompanyFinanceYear",
 *      required={""},
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
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
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCurrent",
 *          description="isCurrent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClosed",
 *          description="isClosed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpSystemID",
 *          description="closedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpID",
 *          description="closedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpName",
 *          description="closedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
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
class CompanyFinanceYear extends Model
{
    public $table = 'companyfinanceyear';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'companyFinanceYearID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'bigginingDate',
        'endingDate',
        'isActive',
        'isCurrent',
        'isClosed',
        'isDeleted',
        'deletedBy',
        'closedByEmpSystemID',
        'closedByEmpID',
        'closedByEmpName',
        'closedDate',
        'comments',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUser',
        'modifiedPc',
        'timeStamp',
        'deleted_at',
        'generateStatus'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyFinanceYearID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isActive' => 'integer',
        'isCurrent' => 'integer',
        'isClosed' => 'integer',
        'closedByEmpSystemID' => 'integer',
        'closedByEmpID' => 'string',
        'closedByEmpName' => 'string',
        'comments' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'generateStatus' => 'integer'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('isDeleted', function (Builder $builder) {
            $builder->where('isDeleted', 0);
        });
    }

    public function created_employee()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserID', 'empID');
    }

    public function modified_employee()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUser', 'empID');
    }

    public function scopeOfCompany($query, $type)
    {
        return $query->where('companySystemID',  $type);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public static function financeYearID($budgetYear, $companySystemID)
    {
        $companyFinanceYear = CompanyFinanceYear::whereYear('bigginingDate', $budgetYear)
                                                ->where('companySystemID', $companySystemID)
                                                ->first();

        if ($companyFinanceYear) {
            return $companyFinanceYear->companyFinanceYearID;
        } else {
            return null;
        }
    }

    public static function budgetYearByDate($date, $companySystemID)
    {
        $companyFinanceYear = CompanyFinanceYear::whereDate('bigginingDate','<=', $date)
                                                ->whereDate('endingDate','>=' ,$date)
                                                ->where('companySystemID', $companySystemID)
                                                ->first();

        if ($companyFinanceYear) {
            return Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');
        } else {
            return date("Y");;
        }
    }

    public static function active_finance_year($company, $date){
        return CompanyFinanceYear::selectRaw("companyFinanceYearID, DATE(bigginingDate) AS startDate, DATE(endingDate) AS endDate, isCurrent")
            ->where('companySystemID', $company)
            ->where('isActive', -1)
            ->whereRaw("( '{$date}' BETWEEN DATE(bigginingDate) AND  DATE(endingDate) ) ")
            ->first();
    }

    public static function checkFinanceYear($companySystemID, $date)
    {
        return CompanyFinanceYear::where('companySystemID', $companySystemID)
                        ->whereRaw('? between bigginingDate and endingDate', $date)
                        ->first();
    }


    public static function budgetYearByFinanceYearID($companyFinanceYearID)
    {
        $companyFinanceYear = CompanyFinanceYear::find($companyFinanceYearID);

        if ($companyFinanceYear) {
            return Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');
        } else {
            return null;
        }
    }

    public static function currentFinanceYear($companySystemID)
    {
        return CompanyFinanceYear::selectRaw("companyFinanceYearID, DATE(bigginingDate) AS startDate, DATE(endingDate) AS endDate")
                        ->where('companySystemID', $companySystemID)
                        ->where('isCurrent', -1)
                        ->where('isActive', -1)
                        ->first();
    }

}
