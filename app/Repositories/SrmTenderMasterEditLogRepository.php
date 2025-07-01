<?php

namespace App\Repositories;

use App\Models\SrmTenderMasterEditLog;
use Illuminate\Contracts\Foundation\Application;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class SrmTenderMasterEditLogRepository
 * @package App\Repositories
 * @version June 3, 2025, 8:06 pm +04
 *
 * @method SrmTenderMasterEditLog findWithoutFail($id, $columns = ['*'])
 * @method SrmTenderMasterEditLog find($id, $columns = ['*'])
 * @method SrmTenderMasterEditLog first($columns = ['*'])
*/
class SrmTenderMasterEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'version_id',
        'level_no',
        'uuid',
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
        'closed_yn',
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
        'document_type',
        'commercial_line_item_status',
        'commercial_ranking_comment',
        'final_tender_award_comment',
        'final_tender_awarded',
        'final_tender_award_email',
        'award_commite_mem_status',
        'final_tender_comment_status',
        'tender_edit_version_id',
        'is_negotiation_started',
        'negotiation_published',
        'negotiation_serial_no',
        'negotiation_code',
        'tender_edit_confirm_id',
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
        'contract_id'
    ];


    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmTenderMasterEditLog::class;
    }

    public function saveTenderMasterHistory($tenderMaster, $versionID=null){
        try{
            return DB::transaction(function () use ($tenderMaster, $versionID) {
                $recordData = $this->prepareRecordData($tenderMaster, $versionID);
                SrmTenderMasterEditLog::create($recordData);
                return ['success' => false, 'message' => 'Success'];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function prepareRecordData($tenderMaster, $versionID){
        $levelNo = $this->model->getLevelNo($tenderMaster['id']);
        $recordData = $tenderMaster->toArray();
        $recordData['level_no'] = $levelNo;
        $recordData['id'] = $tenderMaster['id'];
        $recordData['version_id'] = $versionID;
        return array_intersect_key(
            $recordData,
            array_flip((new SrmTenderMasterEditLog())->getFillable())
        );
    }
}
