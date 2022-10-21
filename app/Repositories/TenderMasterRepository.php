<?php

namespace App\Repositories;

use App\Models\TenderMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderMasterRepository
 * @package App\Repositories
 * @version March 10, 2022, 1:54 pm +04
 *
 * @method TenderMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderMaster find($id, $columns = ['*'])
 * @method TenderMaster first($columns = ['*'])
*/
class TenderMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'bid_submission_opening_date',
        'bid_submission_closing_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'bid_opening_date',
        'bid_opening_end_date',
        'technical_bid_opening_date',
        'technical_bid_closing_date',
        'commerical_bid_opening_date',
        'commerical_bid_closing_date',
        'bid_opening_date_time',
        'bid_opening_end_date_time',
        'technical_bid_opening_date_time',
        'technical_bid_closing_date_time',
        'commerical_bid_opening_date_time',
        'commerical_bid_closing_date_time',
        'document_sales_start_time',
        'document_sales_end_time',
        'pre_bid_clarification_start_time',
        'pre_bid_clarification_end_time',
        'site_visit_start_time',
        'site_visit_end_time',
        'bid_submission_opening_time',
        'bid_submission_closing_time'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderMaster::class;
    }
}
