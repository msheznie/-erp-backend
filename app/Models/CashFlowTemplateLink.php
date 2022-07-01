<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CashFlowTemplateLink",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glDescription",
 *          description="glDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="subCategory",
 *          description="subCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="categoryType",
 *          description="categoryType",
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
class CashFlowTemplateLink extends Model
{

    public $table = 'cash_flow_template_links';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




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
        'createdPCID',
        'createdUserSystemID',
        'modifiedPCID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'templateMasterID' => 'integer',
        'templateDetailID' => 'integer',
        'sortOrder' => 'integer',
        'glAutoID' => 'integer',
        'glCode' => 'string',
        'glDescription' => 'string',
        'subCategory' => 'integer',
        'categoryType' => 'integer',
        'companySystemID' => 'integer',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function scopeOfTemplate($query, $templateMasterID)
    {
        return $query->where('templateMasterID',  $templateMasterID);
    }


    public function subcategory()
    {
        return $this->belongsTo('App\Models\CashFlowTemplateDetail','subCategory','id');
    }

     public function template_category()
    {
        return $this->belongsTo('App\Models\CashFlowTemplateDetail','templateDetailID','id');
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
        return $this->belongsTo('App\Models\CashFlowTemplateDetail','subCategory','id');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccountsAssigned', 'glAutoID', 'chartOfAccountSystemID');
    }
}
