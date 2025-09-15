<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\BankMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentAttachmentsEditLog;
use App\Models\DocumentAttachmentType;
use App\Models\Employee;
use App\Models\EnvelopType;
use App\Models\EvacuationCriteriaScoreConfigLog;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Models\EvaluationType;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\ProcumentActivityEditLog;
use App\Models\PurchaseRequest;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Models\SrmBudgetItem;
use App\Models\SrmDepartmentMaster;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Models\SrmTenderMasterEditLog;
use App\Models\SrmTenderUserAccessEditLog;
use App\Models\SupplierAssigned;
use App\Models\TenderBidFormatMaster;
use App\Models\TenderBoqItemsEditLog;
use App\Models\TenderBudgetItemEditLog;
use App\Models\TenderDepartmentEditLog;
use App\Models\TenderDocumentTypeAssignLog;
use App\Models\TenderDocumentTypes;
use App\Models\TenderMaster;
use App\Models\TenderProcurementCategory;
use App\Models\TenderPurchaseRequestEditLog;
use App\Models\TenderSupplierAssigneeEditLog;
use App\Models\TenderType;
use App\Models\Unit;
use App\Models\YesNoSelection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SrmTenderEditAmendService
{
    public function sectionConfig()
    {
        return [
            'Tender Strategy' => [
                'sectionId' => '1',
                'modelName' => SrmTenderMasterEditLog::class,
                'skippedFields' => [
                    'amd_id', 'version_id', 'level_no', 'is_deleted', 'tender_edit_version_id', 'confirmed_date', 'tender_edit_confirm_id', 'title', 'title_sec_lang', 'budget_document',
                    'description', 'description_sec_lang', 'currency_id', 'procument_cat_id', 'procument_sub_cat_id', 'estimated_value', 'allocated_budget', 'pre_bid_clarification_method',
                    'show_technical_criteria', 'tender_document_fee', 'bank_id' , 'bank_account_id', 'document_sales_start_date', 'document_sales_end_date', 'pre_bid_clarification_start_date', 'pre_bid_clarification_end_date',
                    'site_visit_date', 'site_visit_end_date', 'bid_submission_opening_date', 'bid_submission_closing_date', 'bid_opening_date', 'bid_opening_end_date',
                    'technical_bid_opening_date', 'technical_bid_closing_date', 'commerical_bid_opening_date', 'commerical_bid_closing_date', 'confirmed_by_emp_system_id',
                    'id', 'uuid', 'tender_type_id', 'created_by', 'updated_by', 'company_id', 'document_system_id', 'document_id', 'tender_code', 'serial_number', 'confirmed_yn',
                    'approved', 'approved_date', 'approved_by_user_system_id', 'RollLevForApp_curr', 'approved_by_emp_name', 'published_yn', 'published_at', 'combined_ranking_status',
                    'is_awarded', 'award_comment', 'commercial_ranking_comment', 'final_tender_award_comment', 'final_tender_awarded', 'final_tender_award_email',
                    'award_commite_mem_status', 'final_tender_comment_status', 'negotiation_serial_no', 'negotiation_code', 'is_negotiation_closed',
                    'negotiation_commercial_ranking_line_item_status', 'negotiation_commercial_ranking_comment', 'negotiation_combined_ranking_status',
                    'negotiation_award_comment', 'negotiation_is_awarded', 'tender_award_commite_mem_status', 'doc_verifiy_date', 'commercial_verify_at',
                    'negotiation_doc_verify_comment', 'negotiation_doc_verify_status', 'technical_eval_status', 'doc_verifiy_comment', 'doc_verifiy_status',
                    'doc_verifiy_date', 'doc_verifiy_by_emp', 'negotiation_published', 'is_negotiation_started', 'commercial_line_item_status', 'document_type',
                    'commercial_ranking_line_item_status', 'commercial_verify_by', 'commercial_verify_at', 'commercial_verify_status', 'go_no_go_status',
                    'technical_eval_status'
                ], 'fieldDescriptions' => [
                    'commercial_passing_weightage' => 'Commercial Criteria Passing Weightage',
                    'technical_passing_weightage' => 'Technical Criteria Passing Weightage',
                    'envelop_type_id' => 'Envelope', 'evaluation_type_id' => 'Evaluation',
                    'stage' => 'Stage', 'no_of_alternative_solutions' => 'Number of Alternative Solutions',
                    'commercial_weightage' => 'Commercial Weightage', 'technical_weightage' => 'Technical Weightage',
                    'is_active_go_no_go' => 'Go/No Go Enable', 'min_approval_bid_opening' => 'Min no of approval for bid opening'
                ],
                'fieldMappings' => [
                    'envelop_type_id' => [
                        'model' => EnvelopType::class,
                        'attribute' => 'name',
                        'colName' => 'id'
                    ], 'evaluation_type_id' => [
                        'model' => EvaluationType::class,
                        'attribute' => 'name',
                        'colName' => 'id'
                    ], 'is_active_go_no_go' => [
                        'model' => YesNoSelection::class,
                        'attribute' => 'YesNo',
                        'colName' => 'idyesNoselection'
                    ]
                ]
            ],
            'Bid Minimum Approval Details' => [
                'sectionId' => '1.1',
                'modelName' => SrmTenderBidEmployeeDetailsEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'commercial_eval_remarks', 'commercial_eval_status', 'modify_type', 'remarks', 'status',
                    'tender_award_commite_mem_comment', 'tender_award_commite_mem_status', 'tender_edit_version_id', 'tender_id', 'updated_by', 'level_no', 'is_deleted',
                ],
                'fieldDescriptions' => ['emp_id' => 'Employee'],
                'fieldMappings' => [
                    'emp_id' => [
                        'model' => Employee::class,
                        'attribute' => 'empName',
                        'colName' => 'employeeSystemID'
                    ]
                ]
            ],
            'Bid Opening Members' => [
                'sectionId' => '1.2',
                'modelName' => SrmTenderUserAccessEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'module_id', 'company_id', 'is_deleted'],
                'fieldDescriptions' => ['user_id' => 'Employee'],
                'fieldMappings' => [
                    'user_id' => [
                        'model' => Employee::class,
                        'attribute' => 'empName',
                        'colName' => 'employeeSystemID'
                    ]
                ]
            ],
            'Commercial Bid Opening Members' => [
                'sectionId' => '1.3',
                'modelName' => SrmTenderUserAccessEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'module_id', 'company_id', 'is_deleted'],
                'fieldDescriptions' => ['user_id' => 'Employee'],
                'fieldMappings' => [
                    'user_id' => [
                        'model' => Employee::class,
                        'attribute' => 'empName',
                        'colName' => 'employeeSystemID'
                    ]
                ]
            ],
            'Supplier Ranking Members' => [
                'sectionId' => '1.4',
                'modelName' => SrmTenderUserAccessEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'module_id', 'company_id', 'is_deleted'],
                'fieldDescriptions' => ['user_id' => 'Employee'],
                'fieldMappings' => [
                    'user_id' => [
                        'model' => Employee::class,
                        'attribute' => 'empName',
                        'colName' => 'employeeSystemID'
                    ]
                ]
            ],
            'General Information' => [
                'sectionId' => '2',
                'modelName' => SrmTenderMasterEditLog::class,
                'skippedFields' => [
                    'amd_id', 'version_id', 'level_no', 'is_deleted', 'tender_edit_version_id', 'confirmed_date', 'tender_edit_confirm_id', 'commercial_passing_weightage', 'technical_passing_weightage',
                    'envelop_type_id', 'evaluation_type_id', 'stage', 'no_of_alternative_solutions', 'commercial_weightage', 'technical_weightage', 'is_active_go_no_go',
                    'min_approval_bid_opening', 'confirmed_by_emp_system_id', 'budget_document', 'id', 'uuid', 'tender_type_id', 'created_by',
                    'updated_by', 'company_id', 'document_system_id', 'document_id', 'tender_code', 'serial_number', 'confirmed_yn',
                    'approved', 'approved_date', 'approved_by_user_system_id', 'RollLevForApp_curr', 'approved_by_emp_name', 'published_yn', 'published_at', 'combined_ranking_status',
                    'is_awarded', 'award_comment', 'commercial_ranking_comment', 'final_tender_award_comment', 'final_tender_awarded', 'final_tender_award_email',
                    'award_commite_mem_status', 'final_tender_comment_status', 'negotiation_serial_no', 'negotiation_code', 'is_negotiation_closed',
                    'negotiation_commercial_ranking_line_item_status', 'negotiation_commercial_ranking_comment', 'negotiation_combined_ranking_status',
                    'negotiation_award_comment', 'negotiation_is_awarded',  'tender_award_commite_mem_status', 'doc_verifiy_date', 'commercial_verify_at',
                    'negotiation_doc_verify_comment', 'negotiation_doc_verify_status', 'technical_eval_status', 'doc_verifiy_comment', 'doc_verifiy_status',
                    'doc_verifiy_date', 'doc_verifiy_by_emp', 'negotiation_published', 'is_negotiation_started', 'commercial_line_item_status', 'document_type',
                    'commercial_ranking_line_item_status', 'commercial_verify_by', 'commercial_verify_at', 'commercial_verify_status', 'go_no_go_status',
                    'technical_eval_status'
                ],
                'fieldDescriptions' => [
                    'title' => 'Title', 'title_sec_lang' => 'Title in Secondary', 'description' => 'Description', 'description_sec_lang' => 'Description in Secondary',
                    'currency_id' => 'Currency', 'procument_cat_id' => 'Procurement Category', 'procument_sub_cat_id' => 'Procurement Sub Category', 'estimated_value' => 'Estimated Value',
                    'allocated_budget' => 'Allocated Budget', 'pre_bid_clarification_method' => 'Pre-bid Clarification Method', 'show_technical_criteria' => 'Do not Show Technical Criteria to Supplier',
                    'tender_document_fee' => 'Tender Document Fee', 'bank_id' => 'Bank', 'bank_account_id' => 'Bank Account', 'document_sales_start_date' => 'Document Sale Start Date',
                    'document_sales_end_date' => 'Document Sale End Date', 'pre_bid_clarification_start_date' => 'Pre Bid Clarification Start Date',
                    'pre_bid_clarification_end_date' => 'Pre Bid Clarification End Date', 'site_visit_date' => 'Site Visit Start Date',
                    'site_visit_end_date' => 'Site Visit End Date', 'bid_submission_opening_date' => 'Bid Submission Date (Opening)',
                    'bid_submission_closing_date' => 'Bid Submission Date (Closing)', 'bid_opening_date' => 'Bid Opening Date (Start)',
                    'bid_opening_end_date' => 'Bid Opening Date (End)', 'technical_bid_opening_date' => 'Technical Bid Opening Date',
                    'technical_bid_closing_date' => 'Technical Bid Closing Date', 'commerical_bid_opening_date' => 'Commercial Bid Opening Date', 'commerical_bid_closing_date' => 'Commercial Bid Closing Date',
                ],
                'fieldMappings' => [
                    'currency_id' => [
                        'model' => CurrencyMaster::class,
                        'attribute' => 'CurrencyName',
                        'colName' => 'currencyID'
                    ],'bank_id' => [
                        'model' => BankMaster::class,
                        'attribute' => 'bankName',
                        'colName' => 'bankmasterAutoID'
                    ],'bank_account_id' => [
                        'model' => BankAccount::class,
                        'attribute' => 'AccountNo',
                        'colName' => 'bankAccountAutoID'
                    ],'procument_cat_id' => [
                        'model' => TenderProcurementCategory::class,
                        'attribute' => 'description',
                        'colName' => 'id'
                    ],'procument_sub_cat_id' => [
                        'model' => TenderProcurementCategory::class,
                        'attribute' => 'description',
                        'colName' => 'id'
                    ], 'show_technical_criteria' => [
                        'model' => YesNoSelection::class,
                        'attribute' => 'YesNo',
                        'colName' => 'idyesNoselection'
                    ]
                ]
            ],
            'General Information - Procurement Activity' => [
                'sectionId' => '2.1',
                'modelName' => ProcumentActivityEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'level_no', 'tender_id', 'company_id', 'version_id', 'modify_type', 'master_id', 'ref_log_id', 'updated_by', 'is_deleted'],
                'fieldDescriptions' => ['category_id' => 'Procurement Activity'],
                'fieldMappings' => [
                    'category_id' => [
                        'model' => TenderProcurementCategory::class,
                        'attribute' => 'description',
                        'colName' => 'id'
                    ]
                ]
            ],
            'General Information - Purchase Request' => [
                'sectionId' => '2.2',
                'modelName' => TenderPurchaseRequestEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'company_id', 'is_deleted'],
                'fieldDescriptions' => ['purchase_request_id' => 'Purchase Request'],
                'fieldMappings' => [
                    'purchase_request_id' => [
                        'model' => PurchaseRequest::class,
                        'attribute' => 'purchaseRequestCode',
                        'colName' => 'purchaseRequestID'
                    ]
                ]
            ],
            'General Information - Department' => [
                'sectionId' => '2.3',
                'modelName' => TenderDepartmentEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'company_id', 'is_deleted'],
                'fieldDescriptions' => ['department_id' => 'Department'],
                'fieldMappings' => [
                    'department_id' => [
                        'model' => SrmDepartmentMaster::class,
                        'attribute' => 'description',
                        'colName' => 'id'
                    ]
                ]
            ],
            'General Information - Budget Items' => [
                'sectionId' => '2.4',
                'modelName' => TenderBudgetItemEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'company_id', 'budget_amount', 'is_deleted'],
                'fieldDescriptions' => ['item_id' => 'Budget Item'],
                'fieldMappings' => [
                    'item_id' => [
                        'model' => SrmBudgetItem::class,
                        'attribute' => 'item_name',
                        'colName' => 'id'
                    ]
                ]
            ],
            'Pricing Schedule' => [
                'sectionId' => '3',
                'modelName' => PricingScheduleMasterEditLog::class,
                'skippedFields' => ['amd_id', 'id', 'level_no','tender_id', 'schedule_mandatory', 'boq_status', 'items_mandatory', 'company_id', 'tender_edit_version_id', 'modify_type', 'created_by', 'updated_by', 'master_id', 'red_log_id', 'is_deleted'],
                'fieldDescriptions' => ['scheduler_name' => 'Scheduler Name', 'price_bid_format_id' => 'Price Bid Format', 'status' => 'Status'],
                'fieldMappings' => [
                    'price_bid_format_id' => [
                        'model' => TenderBidFormatMaster::class,
                        'attribute' => 'tender_name',
                        'colName' => 'id'
                    ]
                ]
            ],
            'Pricing Schedule Details' => [
                'sectionId' => '3.1',
                'modelName' => PricingScheduleDetailEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','tender_id', 'bid_format_id', 'bid_format_detail_id', 'amd_pricing_schedule_master_id', 'field_type', 'boq_applicable', 'pricing_schedule_master_id', 'company_id',
                    'tender_ranking_line_item', 'tender_edit_version_id', 'modify_type', 'description', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'master_id', 'ref_log_id', 'is_deleted',
                    'is_disabled', 'formula_string'
                ],
                'fieldDescriptions' => ['label' => 'Description'],
                'fieldMappings' => []
            ],
            'Pricing Schedule Details (Value)' => [
                'sectionId' => '3.2',
                'modelName' => ScheduleBidFormatDetailsLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','bid_format_detail_id', 'amd_bid_format_detail_id', 'schedule_id', 'amd_pricing_schedule_master_id', 'company_id', 'bid_master_id', 'tender_edit_version_id', 'modify_type',
                    'master_id', 'red_log_id', 'updated_by', 'is_deleted'
                ],
                'fieldDescriptions' => ['value' => 'Value'],
                'fieldMappings' => []
            ],
            'Main Works' => [
                'sectionId' => '3.3',
                'modelName' => TenderBoqItemsEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','main_work_id', 'amd_main_work_id', 'company_id', 'tender_ranking_line_item', 'modify_type', 'master_id', 'purchase_request_id', 'created_by',
                    'origin', 'ref_log_id', 'updated_by', 'is_deleted', 'item_primary_code', 'tender_edit_version_id', 'tender_id'
                ],
                'fieldDescriptions' => ['item_name' => 'Item', 'description' => 'Description', 'uom' => 'UOM', 'qty' => 'Qty'],
                'fieldMappings' => [
                    'uom' => [
                        'model' => Unit::class,
                        'attribute' => 'UnitDes',
                        'colName' => 'UnitID'
                    ]
                ]
            ],
            'Go/No Go' => [
                'sectionId' => '4',
                'modelName' => EvaluationCriteriaDetailsEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','tender_id', 'parent_id', 'critera_type_id', 'answer_type_id', 'level', 'is_final_level', 'weightage', 'passing_weightage',
                    'min_value', 'max_value', 'sort_order', 'evaluation_criteria_master_id', 'modify_type', 'master_id', 'ref_log_id', 'created_by', 'tender_version_id', 'updated_by', 'is_deleted'
                ],
                'fieldDescriptions' => ['description' => 'Description'],
                'fieldMappings' => []
            ],
            'Technical Evaluation' => [
                'sectionId' => '5',
                'modelName' => EvaluationCriteriaDetailsEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','tender_id', 'parent_id', 'critera_type_id', 'answer_type_id', 'level', 'is_final_level',
                    'sort_order', 'evaluation_criteria_master_id', 'modify_type', 'master_id', 'ref_log_id', 'created_by', 'tender_version_id',
                    'updated_by', 'is_deleted'
                ],
                'fieldDescriptions' => [
                    'description' => 'Description',
                    'weightage' => 'Weightage',
                    'passing_weightage' => 'Passing Weightage',
                    'min_value' => 'Minimum Value',
                    'max_value' => 'Maximum Value'
                ],
                'fieldMappings' => []
            ],
            'Technical Evaluation - Score Configuration' => [
                'sectionId' => '5.1',
                'modelName' => EvacuationCriteriaScoreConfigLog::class,
                'skippedFields' => [
                    'amd_id', 'version_id', 'level_no','id', 'criteria_detail_id', 'critera_type_id', 'fromTender',
                    'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted',
                ],
                'fieldDescriptions' => [
                    'label' => 'Label',
                    'score' => 'Value'
                ],
                'fieldMappings' => []
            ],
            'Tender Documents' => [
                'sectionId' => '6',
                'modelName' => TenderDocumentTypeAssignLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','tender_id', 'company_id', 'version_id', 'modify_type', 'master_id', 'ref_log_id',
                    'created_by', 'updated_by', 'is_deleted'
                ],
                'fieldDescriptions' => ['document_type_id' => 'Document Type'],
                'fieldMappings' => [
                    'document_type_id' => [
                        'model' => TenderDocumentTypes::class,
                        'attribute' => 'document_type',
                        'colName' => 'id'
                    ]
                ]
            ],
            'Attachments' => [
                'sectionId' => '6.1',
                'modelName' => DocumentAttachmentsEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','companySystemID', 'companyID', 'documentSystemID', 'documentID', 'documentSystemCode', 'approvalLevelOrder',
                    'path', 'originalFileName', 'myFileName', 'docExpirtyDate', 'attachmentType', 'sizeInKbs', 'isUploaded', 'pullFromAnotherDocument', 'is_deleted',
                    'parent_id', 'envelopType', 'order_number', 'isAutoCreateDocument', 'modify_type', 'master_id', 'ref_log_id', 'version_id', 'updated_by'
                ],
                'fieldDescriptions' => ['attachmentDescription' => 'Description'],
                'fieldMappings' => []
            ],
            'Assign Suppliers' => [
                'sectionId' => '7',
                'modelName' => TenderSupplierAssigneeEditLog::class,
                'skippedFields' => [
                    'amd_id', 'id', 'level_no','id', 'tender_master_id', 'created_by', 'updated_by', 'company_id',
                    'mail_sent', 'is_deleted', 'registration_number', 'registration_link_id', 'supplier_email', 'version_id'
                ],
                'fieldDescriptions' => [
                    'supplier_assigned_id' => 'Name',
                    'supplier_name' => 'Name',
                ],
                'fieldMappings' => [
                    'supplier_assigned_id' => [
                        'model' => SupplierAssigned::class,
                        'attribute' => 'supplierName',
                        'colName' => 'supplierAssignedID'
                    ]
                ]
            ]
        ];
    }

    public function getHistoryData($tenderID, $versionID){
        $allChanges = [];
        $getSectionConfigs = self::sectionConfig();

        foreach ($getSectionConfigs as $sectionName => $config)
        {
            $sectionId = $config['sectionId'];
            $modelName = $config['modelName'];
            $skippedFields = $config['skippedFields'] ?? [];
            $fieldDescriptions = $config['fieldDescriptions'] ?? [];
            $fieldMappings = $config['fieldMappings'] ?? [];
            $section = [
                'sectionId' => $sectionId,
                'sectionName' => $sectionName
            ];

            $currentChanges = self::compareSection(
                $tenderID,
                $versionID,
                $section,
                $modelName,
                $skippedFields,
                $fieldDescriptions,
                $fieldMappings
            );

            $allChanges = array_merge($allChanges, $currentChanges);
        }
        return $allChanges;
    }

    private function compareSection(
        $tenderID, $versionID, $section, $modelName, $skippedFields, $fieldDescriptions, $fieldMappings
    )
    {
        $changes = [];
        $sectionName = $section['sectionName'];
        $sectionId = $section['sectionId'];
        $currentRecords = self::getCurrentData($modelName, $versionID, $tenderID, $sectionId);

        if ($currentRecords) {
            foreach ($currentRecords as $currentRecord) {
                $createdAt = $currentRecord->created_at;
                $updatedAt = $currentRecord->updated_at;
                $date = $updatedAt ?? $createdAt ? Carbon::parse($updatedAt ?? $createdAt) : null;

                $previousRecord = self::getPreviousRecords($modelName, $tenderID, $currentRecord, $sectionId, $versionID);
                $fillableFields = (new $modelName())->getFillable();

                $dateComparison = function ($previous, $current) {
                    if (!empty($previous) && !empty($current)) {
                        return Carbon::parse($previous)->toDateString() !== Carbon::parse($current)->toDateString();
                    }
                    return $previous !== $current;
                };

                $comparisonFunctions = [
                    'document_sales_start_date' => $dateComparison,
                    'document_sales_end_date' => $dateComparison,
                    'pre_bid_clarification_start_date' => $dateComparison,
                    'pre_bid_clarification_end_date' => $dateComparison,
                    'site_visit_date' => $dateComparison,
                    'site_visit_end_date' => $dateComparison,
                    'bid_submission_opening_date' => $dateComparison,
                    'bid_submission_closing_date' => $dateComparison,
                    'bid_opening_date' => $dateComparison,
                    'bid_opening_end_date' => $dateComparison,
                    'technical_bid_opening_date' => $dateComparison,
                    'technical_bid_closing_date' => $dateComparison,
                    'commerical_bid_opening_date' => $dateComparison,
                    'commerical_bid_closing_date' => $dateComparison,
                ];
                foreach ($fillableFields as $field)
                {
                    if (in_array($field, $skippedFields))
                    {
                        continue;
                    }

                    $description = $fieldDescriptions[$field] ?? $field;

                    $oldValue = $previousRecord && is_object($previousRecord) ? $previousRecord->$field : null;
                    $newValue = $currentRecord && is_object($currentRecord) ? $currentRecord->$field : null;

                    if (isset($fieldMappings[$field]))
                    {
                        $model = $fieldMappings[$field]['model'];
                        $attribute = $fieldMappings[$field]['attribute'] ?? 'id';
                        $colName = $fieldMappings[$field]['colName'] ?? 'id';

                        $oldValue = $previousRecord && is_object($previousRecord)
                            ? $model::where($colName, $previousRecord->$field)->value($attribute)
                            : null;
                        $newValue = $model::where($colName, $currentRecord->$field)->value($attribute);
                    }

                    if (in_array($field, ['document_sales_start_date', 'document_sales_end_date', 'pre_bid_clarification_start_date',
                        'pre_bid_clarification_end_date', 'site_visit_date', 'site_visit_end_date', 'bid_submission_opening_date',
                        'bid_submission_closing_date', 'bid_opening_date', 'bid_opening_end_date', 'technical_bid_opening_date',
                        'technical_bid_closing_date', 'commerical_bid_opening_date', 'commerical_bid_closing_date']))
                    {
                        $oldValue = $oldValue ? Carbon::parse($oldValue)->toDateTimeString() : null;
                        $newValue = $newValue ? Carbon::parse($newValue)->toDateTimeString() : null;
                    }

                    if($field == 'pre_bid_clarification_method'){
                        $oldValue = self::getPreBidClarificationMethodtype($oldValue);
                        $newValue = self::getPreBidClarificationMethodtype($newValue);
                    }

                    if($field == 'stage'){
                        $oldValue = self::tenderStage($oldValue);
                        $newValue = self::tenderStage($newValue);
                    }
                    if($sectionId == '3' && $field == 'status'){
                        $oldValue = self::pricingScheduleMasterStatus($oldValue);
                        $newValue = self::pricingScheduleMasterStatus($newValue);
                    }

                    $isDifferent = array_key_exists($field, $comparisonFunctions)
                        ? $comparisonFunctions[$field]($oldValue, $newValue)
                        : $oldValue != $newValue;

                    if ($isDifferent)
                    {
                        $changes[] = [
                            'section' => $sectionName,
                            'field' => $description,
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                            'dateTime' => $date
                        ];
                    }
                }
            }
        }


        return $changes;
    }
    public function getCurrentData($modelName, $versionID, $tenderID, $sectionId)
    {
        return $modelName::where(function ($q) use ($versionID, $tenderID, $sectionId){
            $q->when(in_array($sectionId, ['1', '2']), function ($q) use ($versionID, $tenderID) {
                $q->where('version_id', $versionID)
                    ->where('id', $tenderID);
            })->when(in_array($sectionId, ['1.1', '3', '3.1', '3.3']), function ($q) use ($versionID, $tenderID) {
                $q->where('tender_edit_version_id', $versionID)
                    ->where('tender_id', $tenderID);
            })->when(in_array($sectionId, ['1.2', '1.3', '1.4', '2.1', '2.2', '2.3', '2.4', '6']), function ($q) use ($versionID, $tenderID, $sectionId) {
                $q->where('version_id', $versionID)
                    ->where('tender_id', $tenderID);
                if ($sectionId === '1.2') {
                    $q->where('module_id', 1);
                } elseif ($sectionId === '1.3') {
                    $q->where('module_id', 2);
                } elseif ($sectionId === '1.4') {
                    $q->where('module_id', 3);
                }
            })->when($sectionId == '3.2', function ($q) use ($versionID, $tenderID) {
                $q->where('tender_edit_version_id', $versionID);
            })->when(in_array($sectionId, ['4', '5']), function ($q) use ($versionID, $tenderID, $sectionId) {
                $criteria = $sectionId == '4' ? 1 : 2;
                $q->where('tender_version_id', $versionID)
                    ->where('tender_id', $tenderID)
                    ->where('critera_type_id', $criteria);
            })->when($sectionId == '6.1', function ($q) use ($versionID, $tenderID) {
                $q->where('version_id', $versionID)
                    ->whereIn('documentSystemID', [108, 113])
                    ->where('documentSystemCode', $tenderID);
            })->when($sectionId == '7', function ($q) use ($versionID, $tenderID) {
                $q->where('version_id', $versionID)
                    ->where('tender_master_id', $tenderID);
            })->when($sectionId == '5.1', function ($q) use ($versionID, $tenderID) {
                $q->where('version_id', $versionID);
            });
        })->where('is_deleted', 0)->get();
    }

    public function getPreviousRecords($modelName, $tenderID, $currentRecord, $sectionId, $versionID)
    {
        $currentID = $currentRecord->id ?? 0;
        $sectionArr = ['1.1', '1.2', '1.3', '1.4', '2.1', '2.2', '2.3', '2.4', '3', '3.1', '3.3', '4', '5', '6'];
        return $modelName::where('level_no', '<', $currentRecord->level_no)
            ->where(function ($q) use ($tenderID, $sectionId, $currentID, $versionID, $sectionArr) {
                $q->when(in_array($sectionId, ['1', '2']), function ($q) use ($tenderID) {
                    $q->where('id', $tenderID);
                })->when(in_array($sectionId, $sectionArr), function ($q) use ($tenderID, $currentID, $sectionId) {
                        $q->where('id', $currentID);
                        $q->where('tender_id', $tenderID);

                        if ($sectionId === '1.2') {
                            $q->where('module_id', 1);
                        } elseif ($sectionId === '1.3') {
                            $q->where('module_id', 2);
                        } elseif ($sectionId === '1.4') {
                            $q->where('module_id', 3);
                        }
                })->when(in_array($sectionId, ['5.1', '3.2']), function ($q) use ($currentID, $versionID) {
                    $q->where('id', $currentID);
                })->when($sectionId == '6.1', function ($q) use ($currentID, $tenderID) {
                    $q->where('id', $currentID)
                        ->whereIn('documentSystemID', [108, 113])
                        ->where('documentSystemCode', $tenderID);
                })->when($sectionId == '7', function ($q) use ($currentID, $tenderID) {
                    $q->where('id', $currentID)
                        ->where('tender_master_id', $tenderID);
                });
            })
            ->where('is_deleted', 0)
            ->orderBy('level_no', 'desc')
            ->first();
    }
    public static function getPreBidClarificationMethodtype($type): string{
        switch($type){
            case 0:
                return 'Offline';
            case 1 :
                return 'Online';
            case 2 :
                return 'Both';
            default:
                return '';
        }
    }
    public static function tenderStage($stage): string
    {
        return $stage == 1 ? 'Single Stage' : 'Two Stage';
    }
    public static function pricingScheduleMasterStatus($status){
        return $status == 1 ? 'Completed' : 'Pending';
    }
    public function rejectDocumentRequestChanges($tenderID){
        try {
            $tenderMaster = TenderMaster::getTenderMasterData($tenderID);
            if(empty($tenderMaster)){
                return ['success' => false, 'message' => trans('srm_tender_rfx.tender_data_not_found')];
            }

            $tenderMasterID = $tenderMaster->id;
            $tenderHistory = SrmTenderMasterEditLog::tenderMasterHistory($tenderMasterID);
            if(empty($tenderHistory)){
                return ['success' => false, 'message' => trans('srm_tender_rfx.tender_history_data_not_found')];
            }
            $versionID = $tenderHistory->version_id;
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $sections = $this->sectionConfig();
                $allSectionIDs = [
                    '1', '1.1', '1.2', '2', '2.1', '2.2', '2.3', '2.4', '3', '3.1', '3.2', '3.3', '4', '5', '5.1', '6',
                    '6.1', '7'
                ];

                foreach ($sections as $section) {
                    $model = $section['modelName'] ?? null;
                    $sectionId = $section['sectionId'] ?? '';

                    if ($model && class_exists($model) && in_array($sectionId, $allSectionIDs)) {
                        $model::when(in_array($sectionId, ['1', '2', '1.2', '2.1', '2.2', '2.3', '2.4', '5.1', '6', '6.1', '7']), function ($q) use ($versionID){
                            $q->where('version_id', $versionID);
                        })->when(in_array($sectionId, ['1.1', '3', '3.1', '3.2', '3.3']), function ($q) use ($versionID){
                            $q->where('tender_edit_version_id', $versionID);
                        })->when(in_array($sectionId, ['4', '5']), function ($q) use ($versionID){
                            $q->where('tender_version_id', $versionID);
                        })->where('is_deleted', 0)->update(['is_deleted' => 1]);
                    }
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $ex->getMessage()])];
        }
    }
}
