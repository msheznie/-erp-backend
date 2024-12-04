<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
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


    protected $appends = array(
                            'document_sales_start_time'
                            ,'document_sales_end_time'
                            ,'pre_bid_clarification_start_time'
                            ,'pre_bid_clarification_end_time'
                            ,'site_visit_start_time'
                            ,'site_visit_end_time'
                            ,'bid_submission_opening_time'
                            ,'bid_submission_closing_time'
                            ,'bid_opening_date_time'
                            ,'bid_opening_end_date_time'
                            ,'technical_bid_opening_date_time'
                            ,'technical_bid_closing_date_time'
                            ,'commerical_bid_opening_date_time'
                            ,'commerical_bid_closing_date_time');


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
        'technical_passing_weightage',
        'min_approval_bid_opening',
        'bid_opening_date',
        'bid_opening_end_date',
        'technical_bid_opening_date',
        'technical_bid_closing_date',
        'commerical_bid_opening_date',
        'commerical_bid_closing_date',
        'doc_verifiy_by_emp',
        'doc_verifiy_date',
        'doc_verifiy_status',
        'doc_verifiy_comment',
        'published_at',
        'technical_eval_status',
        'go_no_go_status',
        'commercial_verify_status',
        'commercial_verify_at',
        'commercial_verify_by',
        'commercial_ranking_line_item_status',
        'combined_ranking_status',
        'is_awarded',
        'award_comment',
        'commercial_line_item_status',
        'commercial_ranking_comment',
        'document_type',
        'final_tender_award_comment',
        'final_tender_awarded',
        'final_tender_award_email',
        'award_commite_mem_status',
        'final_tender_comment_status',
        'tender_edit_version_id',
        'is_negotiation_started',
        'tender_edit_confirm_id',
        'negotiation_published',
        'negotiation_code',
        'negotiation_serial_no',
        'is_negotiation_closed',
        'negotiation_commercial_ranking_line_item_status',
        'negotiation_commercial_ranking_comment',
        'negotiation_combined_ranking_status',
        'negotiation_award_comment',
        'negotiation_is_awarded',
        'negotiation_doc_verify_comment',
        'negotiation_doc_verify_status',
        'show_technical_criteria',
        'isDelegation',
        'uuid'
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
        'min_approval_bid_opening' => 'integer',
        'bid_opening_date'  => 'datetime',
        'bid_opening_end_date'  => 'datetime',
        'technical_bid_opening_date'  => 'datetime',
        'technical_bid_closing_date'  => 'datetime',
        'commerical_bid_opening_date'  => 'datetime',
        'commerical_bid_closing_date'  => 'datetime',
        'doc_verifiy_status' => 'integer',
        'published_at' => 'datetime',
        'document_type' => 'integer',
        'is_negotiation_started'=> 'integer',
        'negotiation_published'=> 'integer',
        'negotiation_code'=> 'string',
        'negotiation_serial_no'=> 'integer',
        'is_negotiation_closed'=> 'integer',
        'negotiation_commercial_ranking_line_item_status' => 'integer',
        'negotiation_commercial_ranking_comment' => 'string',
        'negotiation_combined_ranking_status' => 'integer',
        'negotiation_award_comment' => 'string',
        'negotiation_is_awarded' => 'integer',
        'negotiation_doc_verify_comment' => 'string',
        'negotiation_doc_verify_status'  => 'integer',
        'uuid'  => 'string'
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

    public function srm_bid_submission_master()
    {
        return $this->hasMany('App\Models\BidSubmissionMaster', 'tender_id', 'id');
    }

    public function DocumentAttachments()
    {
        return $this->hasMany('App\Models\DocumentAttachments', 'documentSystemCode', 'id');
    }

    public function evaluation_type()
    {
        return $this->hasOne('App\Models\EvaluationType', 'id', 'evaluation_type_id');
    }

    public function ranking_supplier()
    {
        return $this->hasOne('App\Models\TenderFinalBids', 'tender_id', 'id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','company_id','companySystemID');
    }

    public function tender_negotiation() {
        return $this->hasMany('App\Models\TenderNegotiation','srm_tender_master_id','id');
    }

    public function criteriaDetails()
    {
        return $this->hasMany('App\Models\EvaluationCriteriaDetails', 'tender_id', 'id');
    }

    
    public function getDocumentSalesStartTimeAttribute() {
        if($this->document_sales_start_date) {
            $time = new Carbon($this->document_sales_start_date);
            return $time->format('Y-m-d H:i:s');
        }else {
            return null;
        }

    }

    public function getDocumentSalesEndTimeAttribute() {
        if($this->document_sales_end_date) {
            $time = new Carbon($this->document_sales_end_date);
            return $time->format('Y-m-d H:i:s');
        }else {
            return null;
        }

    }

    public function getPreBidClarificationStartTimeAttribute() {
        if($this->pre_bid_clarification_start_date) {
            $time = new Carbon($this->pre_bid_clarification_start_date);
            return $time->format('Y-m-d H:i:s'); 
        }else {
             return null;
        }

    }

    
    public function getPreBidClarificationEndTimeAttribute() {
        if($this->pre_bid_clarification_end_date) {
            $time = new Carbon($this->pre_bid_clarification_end_date);
            return $time->format('Y-m-d H:i:s'); 
        }else {
            return null;
        }

    }


    public function getSiteVisitStartTimeAttribute() {
        if($this->site_visit_date) {
            $time = new Carbon($this->site_visit_date);
            return $time->format('Y-m-d H:i:s'); 
        }else {
            return null;
        }

    }

    public function getSiteVisitEndTimeAttribute() {
        if($this->site_visit_end_date) {
            $time = new Carbon($this->site_visit_end_date);
            return $time->format('Y-m-d H:i:s');   
        }else {
            return null;
        }

    }

    public function getBidSubmissionOpeningTimeAttribute() {
        if($this->bid_submission_opening_date) {
            $time = new Carbon($this->bid_submission_opening_date);
            return $time->format('Y-m-d H:i:s');   
        }else {
            return null;
        }

    }

    public function getBidSubmissionClosingTimeAttribute() {
        if($this->bid_submission_closing_date) {
            $time = new Carbon($this->bid_submission_closing_date);
            return $time->format('Y-m-d H:i:s');   
        }else {
            return null;
        }

    }


    public function getBidOpeningDateTimeAttribute() {
        if($this->bid_opening_date) {
            $time = new Carbon($this->bid_opening_date);
            return $time->format('Y-m-d H:i:s'); 
        }else {
            return null;
        }
  
    }

    public function getBidOpeningEndDateTimeAttribute() {
        if($this->bid_opening_end_date) {
            $time = new Carbon($this->bid_opening_end_date);
            return $time->format('Y-m-d H:i:s');   
        }else {
            return null;
        }

    }

    public function getTechnicalBidOpeningDateTimeAttribute() {
        if($this->technical_bid_opening_date) {
            $time = new Carbon($this->technical_bid_opening_date);
            return $time->format('Y-m-d H:i:s'); 
        }else {
            return null;
        }
  
    }

    public function getTechnicalBidClosingDateTimeAttribute() {
        if($this->technical_bid_closing_date) {
            $time = new Carbon($this->technical_bid_closing_date);
            return $time->format('Y-m-d H:i:s');  
        }else {
            return null;
        }
 
    }


    public function getCommericalBidOpeningDateTimeAttribute() {
        if($this->commerical_bid_opening_date) {
            $time = new Carbon($this->commerical_bid_opening_date);
            return $time->format('Y-m-d H:i:s');  
        }else {
            return null;
        }
 
    }

    public function getCommericalBidClosingDateTimeAttribute() {
        if($this->commerical_bid_closing_date) {
            $time = new Carbon($this->commerical_bid_closing_date);
            return $time->format('Y-m-d H:i:s');  
        }else {
            return null;
        }
 
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    public function modifiedBy()
    {
        return $this->belongsTo('App\Models\Employee', 'updated_by', 'employeeSystemID');
    }

    public function approvedBy()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'id');
    }
     
    public function awardedSupplier()
    {
        return $this->hasOne('App\Models\TenderFinalBids', 'tender_id', 'id')->where('award', 1);
    }

    public function srmTenderMasterSuppliers()
    {
        return $this->hasOne('App\Models\TenderMasterSupplier', 'tender_master_id', 'id');
    }

    public function approvedRejectStatus()
    {
        return $this->hasOne('App\Models\DocumentApproved', 'documentSystemCode', 'id')->where('status',1);
    }

    public function tenderUserAccess()
    {
        return $this->hasMany('App\Models\SRMTenderUserAccess', 'tender_id', 'id');
    }
    public function tenderBidMinimumApproval()
    {
        return $this->hasMany('App\Models\SrmTenderBidEmployeeDetails', 'tender_id', 'id');
    }
    public static function getTenderDidOpeningDates($tenderId, $companyId)
    {
        return TenderMaster::select('id','stage', 'bid_opening_date',
            'technical_bid_opening_date', 'bid_opening_end_date', 'technical_bid_closing_date')
            ->where('id', $tenderId)
            ->where('company_id', $companyId)
            ->first();
    }

    public static function getTenderByUuid($tenderUuid)
    {
        return self::where('uuid', $tenderUuid)
            ->first();
    }

    public static function getTenderPOData($tenderId, $companyId)
    {
        $tender = TenderMaster::select('id', 'title', 'tender_code', 'currency_id')
            ->with(['ranking_supplier' => function ($q) {
                $q->select('id', 'supplier_id', 'tender_id')->where('award', 1)
                    ->with(['supplier' => function ($q) {
                        $q->select('id', 'supplier_master_id');
                    }]);
            }])
            ->where('uuid', $tenderId)
            ->where('company_id', $companyId)
            ->first();

        if (!$tender) {
            return null;
        }

        return [
            'title' => $tender->title,
            'tender_code' => $tender->tender_code,
            'currency_id' => $tender->currency_id,
            'ranking_supplier' => $tender->ranking_supplier ? [
                'id' => $tender->ranking_supplier->id,
                'supplier_id' => $tender->ranking_supplier->supplier_id,
                'supplier' => $tender->ranking_supplier->supplier ? [
                    'id' => $tender->ranking_supplier->supplier->id,
                    'supplier_master_id' => $tender->ranking_supplier->supplier->supplier_master_id,
                ] : null,
            ] : null,
        ];

    }


    public function srmTenderPo()
    {
        return $this->hasOne('App\Models\SrmTenderPo', 'tender_id', 'id')->where('status', 1);
    }

}
