<?php
/**
 * =============================================
 * -- File Name : ReportTemplateLinks.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 30- January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateLinks",
 *      required={""},
 *      @SWG\Property(
 *          property="linkID",
 *          description="linkID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateMasterID",
 *          description="templateMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateDetailID",
 *          description="templateDetailID",
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
 *          property="glAutoID",
 *          description="glAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subCategory",
 *          description="subCategory",
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
class ReportTemplateLinks extends Model
{
     use Compoships;
    public $table = 'erp_companyreporttemplatelinks';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'linkID';

    public $fillable = [
        'templateMasterID',
        'templateDetailID',
        'sortOrder',
        'glAutoID',
        'glCode',
        'glDescription',
        'subCategory',
        'categoryType',
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
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'linkID' => 'integer',
        'templateMasterID' => 'integer',
        'templateDetailID' => 'integer',
        'sortOrder' => 'integer',
        'glAutoID' => 'integer',
        'glCode' => 'string',
        'glDescription' => 'string',
        'subCategory' => 'integer',
        'categoryType' => 'integer',
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

    public function scopeOfTemplate($query, $templateMasterID)
    {
        return $query->where('templateMasterID',  $templateMasterID);
    }


    public function subcategory()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails','subCategory','detID');
    }

     public function template_category()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails','templateDetailID','detID');
    }

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount','glAutoID','chartOfAccountSystemID');
    }

    public function general_ledger(){
        return $this->hasMany('App\Models\GeneralLedger','chartOfAccountSystemID','glAutoID');
    }

    public function subcategory_detail()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails','subCategory','detID');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccountsAssigned', 'glAutoID', 'chartOfAccountSystemID');
    }
    
    public function items()
    {
        return $this->hasMany('App\Models\Budjetdetails', ['chartOfAccountID', 'templateDetailID'], ['glAutoID', 'templateDetailID']);
    }

     public function items_refferd()
    {
        return $this->hasMany('App\Models\BudgetDetailsRefferedHistory', ['chartOfAccountID', 'templateDetailID'], ['glAutoID', 'templateDetailID']);
    }
}
