<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="TenderMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="title_sec_lang",
 *          description="title_sec_lang",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description_sec_lang",
 *          description="description_sec_lang",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tender_type_id",
 *          description="use srm_tender_type table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currency_id",
 *          description="use currencymaster table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="envelop_type_id",
 *          description="use srm_tender_envelop_type table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="procument_cat_id",
 *          description="use srm_tender_procument_category table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="procument_sub_cat_id",
 *          description="use srm_tender_procument_category table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="evaluation_type_id",
 *          description="use srm_tender_evaluation_type table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="estimated_value",
 *          description="estimated_value",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="allocated_budget",
 *          description="allocated_budget",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="budget_document",
 *          description="budget_document",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tender_document_fee",
 *          description="tender_document_fee",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="bank_id",
 *          description="bank_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bank_account_id",
 *          description="bank_account_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_sales_start_date",
 *          description="document_sales_start_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="document_sales_end_date",
 *          description="document_sales_end_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="pre_bid_clarification_start_date",
 *          description="pre_bid_clarification_start_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="pre_bid_clarification_end_date",
 *          description="pre_bid_clarification_end_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="pre_bid_clarification_method",
 *          description="0 offline 1 online",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="site_visit_date",
 *          description="site_visit_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *     @SWG\Property(
 *          property="site_visit_end_date",
 *          description="site_visit_end_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="bid_submission_opening_date",
 *          description="bid_submission_opening_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="bid_submission_closing_date",
 *          description="bid_submission_closing_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deleted_at",
 *          description="deleted_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="deleted_by",
 *          description="deleted_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderMaster extends Model
{
    use SoftDeletes;
    public $table = 'srm_tender_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'title',
        'title_sec_lang',
        'description',
        'description_sec_lang',
        'tender_type_id',
        'currency_id',
        'envelop_type_id',
        'procument_cat_id',
        'procument_sub_cat_id',
        'evaluation_type_id',
        'estimated_value',
        'allocated_budget',
        'budget_document',
        'tender_document_fee',
        'bank_id',
        'bank_account_id',
        'document_sales_start_date',
        'document_sales_end_date',
        'pre_bid_clarification_start_date',
        'pre_bid_clarification_end_date',
        'pre_bid_clarification_method',
        'site_visit_date',
        'site_visit_end_date',
        'bid_submission_opening_date',
        'bid_submission_closing_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'document_system_id',
        'document_id',
        'tender_code',
        'serial_number',
        'confirmed_yn',
        'confirmed_by_emp_system_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved',
        'approved_date',
        'approved_by_user_system_id',
        'approval_remarks',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'approved_by_emp_name',
        'published_yn',
        'stage',
        'no_of_alternative_solutions',
        'commercial_weightage',
        'technical_weightage',
        'is_active_go_no_go',
        'commercial_passing_weightage',
        'technical_passing_weightage'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'title_sec_lang' => 'string',
        'description' => 'string',
        'description_sec_lang' => 'string',
        'tender_type_id' => 'integer',
        'currency_id' => 'integer',
        'envelop_type_id' => 'integer',
        'procument_cat_id' => 'integer',
        'procument_sub_cat_id' => 'integer',
        'evaluation_type_id' => 'integer',
        'estimated_value' => 'float',
        'allocated_budget' => 'float',
        'budget_document' => 'string',
        'tender_document_fee' => 'float',
        'bank_id' => 'integer',
        'bank_account_id' => 'integer',
        'document_sales_start_date' => 'datetime',
        'document_sales_end_date' => 'datetime',
        'pre_bid_clarification_start_date' => 'datetime',
        'pre_bid_clarification_end_date' => 'datetime',
        'pre_bid_clarification_method' => 'integer',
        'site_visit_date' => 'datetime',
        'site_visit_end_date' => 'datetime',
        'bid_submission_opening_date' => 'datetime',
        'bid_submission_closing_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'company_id' => 'integer',
        'document_system_id' => 'integer',
        'document_id' => 'string',
        'tender_code' => 'string',
        'serialNumber' => 'integer',
        'confirmed_yn' => 'integer',
        'confirmed_by_emp_system_id' => 'integer',
        'confirmed_by_name' => 'string',
        'confirmed_date' => 'datetime',
        'approved' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_user_system_id' => 'integer',
        'approval_remarks' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'approved_by_emp_name' => 'string',
        'published_yn' => 'integer',
        'stage' => 'integer',
        'no_of_alternative_solutions' => 'integer',
        'commercial_weightage' => 'integer',
        'technical_weightage' => 'integer',
        'is_active_go_no_go' => 'integer',
        'commercial_passing_weightage'=> 'integer',
        'technical_passing_weightage'=> 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function tender_type()
    {
        return $this->hasOne('App\Models\TenderType', 'id', 'tender_type_id');
    }

    public function envelop_type()
    {
        return $this->hasOne('App\Models\EnvelopType', 'id', 'envelop_type_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Models\CurrencyMaster', 'currencyID', 'currency_id');
    }

    public function procument_activity()
    {
        return $this->hasMany('App\Models\ProcumentActivity', 'tender_id', 'id');
    }

    public function srmTenderMasterSupplier()
    {
        return $this->hasOne('App\Models\TenderMasterSupplier', 'tender_master_id', 'id');
    }
    public function tenderFaq()
    {
        return $this->hasMany('App\Models\TenderFaq', 'tender_master_id', 'id');

    } 
    public function tenderPreBidClarification()
    {
        return $this->hasMany('App\Models\TenderBidClarifications', 'tender_master_id', 'id');
    } 
 
    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmed_by_emp_system_id', 'employeeSystemID');
    }

    public function tenderSupplierAssignee()
    {
        return $this->hasMany('App\Models\TenderSupplierAssignee', 'tender_master_id', 'id');
    }
}
