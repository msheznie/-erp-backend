<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SrmTenderMasterEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="version_id",
 *          description="version_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="level_no",
 *          description="level_no",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="uuid",
 *          description="uuid",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="title",
 *          description="title",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="title_sec_lang",
 *          description="title_sec_lang",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="description_sec_lang",
 *          description="description_sec_lang",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tender_type_id",
 *          description="tender_type_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="currency_id",
 *          description="currency_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="envelop_type_id",
 *          description="envelop_type_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="procument_cat_id",
 *          description="procument_cat_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="procument_sub_cat_id",
 *          description="procument_sub_cat_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="evaluation_type_id",
 *          description="evaluation_type_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="estimated_value",
 *          description="estimated_value",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="allocated_budget",
 *          description="allocated_budget",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="budget_document",
 *          description="budget_document",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tender_document_fee",
 *          description="tender_document_fee",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="bank_id",
 *          description="bank_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="bank_account_id",
 *          description="bank_account_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="document_sales_start_date",
 *          description="document_sales_start_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="document_sales_end_date",
 *          description="document_sales_end_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="pre_bid_clarification_start_date",
 *          description="pre_bid_clarification_start_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="pre_bid_clarification_end_date",
 *          description="pre_bid_clarification_end_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="pre_bid_clarification_method",
 *          description="pre_bid_clarification_method",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="site_visit_date",
 *          description="site_visit_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="site_visit_end_date",
 *          description="site_visit_end_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="bid_submission_opening_date",
 *          description="bid_submission_opening_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="bid_submission_closing_date",
 *          description="bid_submission_closing_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="created_by",
 *          description="created_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="deleted_at",
 *          description="deleted_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="deleted_by",
 *          description="deleted_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="document_system_id",
 *          description="document_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="document_id",
 *          description="document_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tender_code",
 *          description="tender_code",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="serial_number",
 *          description="serial_number",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmed_yn",
 *          description="confirmed_yn",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmed_by_emp_system_id",
 *          description="confirmed_by_emp_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmed_by_name",
 *          description="confirmed_by_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="confirmed_date",
 *          description="confirmed_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="approved",
 *          description="approved",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approved_date",
 *          description="approved_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="approved_by_user_system_id",
 *          description="approved_by_user_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approval_remarks",
 *          description="approval_remarks",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approved_by_emp_name",
 *          description="approved_by_emp_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="published_yn",
 *          description="published_yn",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="closed_yn",
 *          description="closed_yn",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="stage",
 *          description="stage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="no_of_alternative_solutions",
 *          description="no_of_alternative_solutions",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="commercial_weightage",
 *          description="commercial_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="technical_weightage",
 *          description="technical_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="is_active_go_no_go",
 *          description="is_active_go_no_go",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="commercial_passing_weightage",
 *          description="commercial_passing_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="technical_passing_weightage",
 *          description="technical_passing_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="min_approval_bid_opening",
 *          description="min_approval_bid_opening",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="bid_opening_date",
 *          description="bid_opening_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="bid_opening_end_date",
 *          description="bid_opening_end_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="technical_bid_opening_date",
 *          description="technical_bid_opening_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="technical_bid_closing_date",
 *          description="technical_bid_closing_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="commerical_bid_opening_date",
 *          description="commerical_bid_opening_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="commerical_bid_closing_date",
 *          description="commerical_bid_closing_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="doc_verifiy_by_emp",
 *          description="doc_verifiy_by_emp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="doc_verifiy_date",
 *          description="doc_verifiy_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="doc_verifiy_status",
 *          description="doc_verifiy_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="doc_verifiy_comment",
 *          description="doc_verifiy_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="published_at",
 *          description="published_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="technical_eval_status",
 *          description="technical_eval_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="go_no_go_status",
 *          description="go_no_go_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="commercial_verify_status",
 *          description="commercial_verify_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="commercial_verify_at",
 *          description="commercial_verify_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="commercial_verify_by",
 *          description="commercial_verify_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="commercial_ranking_line_item_status",
 *          description="commercial_ranking_line_item_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="combined_ranking_status",
 *          description="combined_ranking_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="is_awarded",
 *          description="is_awarded",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="award_comment",
 *          description="award_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="document_type",
 *          description="document_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="commercial_line_item_status",
 *          description="commercial_line_item_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="commercial_ranking_comment",
 *          description="commercial_ranking_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="final_tender_award_comment",
 *          description="final_tender_award_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="final_tender_awarded",
 *          description="final_tender_awarded",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="final_tender_award_email",
 *          description="final_tender_award_email",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="award_commite_mem_status",
 *          description="award_commite_mem_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="final_tender_comment_status",
 *          description="final_tender_comment_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="tender_edit_version_id",
 *          description="tender_edit_version_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="is_negotiation_started",
 *          description="is_negotiation_started",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="negotiation_published",
 *          description="negotiation_published",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="negotiation_serial_no",
 *          description="negotiation_serial_no",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="negotiation_code",
 *          description="negotiation_code",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tender_edit_confirm_id",
 *          description="tender_edit_confirm_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="is_negotiation_closed",
 *          description="is_negotiation_closed",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="negotiation_commercial_ranking_line_item_status",
 *          description="negotiation_commercial_ranking_line_item_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="negotiation_commercial_ranking_comment",
 *          description="negotiation_commercial_ranking_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="negotiation_combined_ranking_status",
 *          description="negotiation_combined_ranking_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="negotiation_award_comment",
 *          description="negotiation_award_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="negotiation_is_awarded",
 *          description="negotiation_is_awarded",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="negotiation_doc_verify_comment",
 *          description="negotiation_doc_verify_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="negotiation_doc_verify_status",
 *          description="negotiation_doc_verify_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="show_technical_criteria",
 *          description="show_technical_criteria",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isDelegation",
 *          description="isDelegation",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="contract_id",
 *          description="contract_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SrmTenderMasterEditLog extends Model
{

    public $table = 'srm_tender_master_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';

    protected $appends = [
        'document_sales_start_time',
        'document_sales_end_time',
        'pre_bid_clarification_start_time',
        'pre_bid_clarification_end_time',
        'site_visit_start_time',
        'site_visit_end_time',
        'bid_submission_opening_time',
        'bid_submission_closing_time',
        'bid_opening_date_time',
        'bid_opening_end_date_time',
        'technical_bid_opening_date_time',
        'technical_bid_closing_date_time',
        'commerical_bid_opening_date_time',
        'commerical_bid_closing_date_time'
    ];


    public $fillable = [
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
        'contract_id',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'id' => 'integer',
        'version_id' => 'integer',
        'level_no' => 'integer',
        'uuid' => 'string',
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
        'serial_number' => 'integer',
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
        'closed_yn' => 'integer',
        'stage' => 'integer',
        'no_of_alternative_solutions' => 'integer',
        'commercial_weightage' => 'integer',
        'technical_weightage' => 'integer',
        'is_active_go_no_go' => 'integer',
        'commercial_passing_weightage' => 'integer',
        'technical_passing_weightage' => 'integer',
        'min_approval_bid_opening' => 'integer',
        'bid_opening_date' => 'datetime',
        'bid_opening_end_date' => 'datetime',
        'technical_bid_opening_date' => 'datetime',
        'technical_bid_closing_date' => 'datetime',
        'commerical_bid_opening_date' => 'datetime',
        'commerical_bid_closing_date' => 'datetime',
        'doc_verifiy_by_emp' => 'integer',
        'doc_verifiy_date' => 'datetime',
        'doc_verifiy_status' => 'boolean',
        'doc_verifiy_comment' => 'string',
        'published_at' => 'datetime',
        'technical_eval_status' => 'boolean',
        'go_no_go_status' => 'boolean',
        'commercial_verify_status' => 'boolean',
        'commercial_verify_at' => 'datetime',
        'commercial_verify_by' => 'integer',
        'commercial_ranking_line_item_status' => 'boolean',
        'combined_ranking_status' => 'boolean',
        'is_awarded' => 'boolean',
        'award_comment' => 'string',
        'document_type' => 'integer',
        'commercial_line_item_status' => 'boolean',
        'commercial_ranking_comment' => 'string',
        'final_tender_award_comment' => 'string',
        'final_tender_awarded' => 'boolean',
        'final_tender_award_email' => 'boolean',
        'award_commite_mem_status' => 'boolean',
        'final_tender_comment_status' => 'boolean',
        'tender_edit_version_id' => 'integer',
        'is_negotiation_started' => 'integer',
        'negotiation_published' => 'integer',
        'negotiation_serial_no' => 'integer',
        'negotiation_code' => 'string',
        'tender_edit_confirm_id' => 'integer',
        'is_negotiation_closed' => 'integer',
        'negotiation_commercial_ranking_line_item_status' => 'boolean',
        'negotiation_commercial_ranking_comment' => 'string',
        'negotiation_combined_ranking_status' => 'boolean',
        'negotiation_award_comment' => 'string',
        'negotiation_is_awarded' => 'boolean',
        'negotiation_doc_verify_comment' => 'string',
        'negotiation_doc_verify_status' => 'boolean',
        'show_technical_criteria' => 'integer',
        'isDelegation' => 'boolean',
        'contract_id' => 'integer',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    public function procument_activity()
    {
        return $this->hasMany('App\Models\ProcumentActivityEditLog', 'tender_id', 'id');
    }
    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmed_by_emp_system_id', 'employeeSystemID');
    }
    public function approvedRejectStatus()
    {
        return $this->hasOne('App\Models\DocumentApproved', 'documentSystemCode', 'id')->where('status',1);
    }

    public static function getLevelNo($tender_id){
        return max(1, (self::where('id', $tender_id)->max('level_no') ?? 0) + 1);
    }

    public function getDocumentSalesStartTimeAttribute() {
        if (!$this->document_sales_start_date) {
            return null;
        }
        return Carbon::parse($this->document_sales_start_date)->format('Y-m-d H:i:s');
    }

    public function getDocumentSalesEndTimeAttribute() {
        if (!$this->document_sales_end_date) {
            return null;
        }
        return Carbon::parse($this->document_sales_end_date)->format('Y-m-d H:i:s');
    }

    public function getPreBidClarificationStartTimeAttribute() {
        if (!$this->pre_bid_clarification_start_date) {
            return null;
        }
        return Carbon::parse($this->pre_bid_clarification_start_date)->format('Y-m-d H:i:s');
    }


    public function getPreBidClarificationEndTimeAttribute() {
        if (!$this->pre_bid_clarification_end_date) {
            return null;
        }
        return Carbon::parse($this->pre_bid_clarification_end_date)->format('Y-m-d H:i:s');
    }


    public function getSiteVisitStartTimeAttribute() {
        if (!$this->site_visit_date) {
            return null;
        }
        return Carbon::parse($this->site_visit_date)->format('Y-m-d H:i:s');
    }

    public function getSiteVisitEndTimeAttribute() {
        if (!$this->site_visit_end_date) {
            return null;
        }
        return Carbon::parse($this->site_visit_end_date)->format('Y-m-d H:i:s');
    }

    public function getBidSubmissionOpeningTimeAttribute() {
        if (!$this->bid_submission_opening_date) {
            return null;
        }
        return Carbon::parse($this->bid_submission_opening_date)->format('Y-m-d H:i:s');
    }

    public function getBidSubmissionClosingTimeAttribute() {
        if (!$this->bid_submission_closing_date) {
            return null;
        }
        return Carbon::parse($this->bid_submission_closing_date)->format('Y-m-d H:i:s');
    }


    public function getBidOpeningDateTimeAttribute() {
        if (!$this->bid_opening_date) {
            return null;
        }
        return Carbon::parse($this->bid_opening_date)->format('Y-m-d H:i:s');
    }

    public function getBidOpeningEndDateTimeAttribute() {
        if (!$this->bid_opening_end_date) {
            return null;
        }
        return Carbon::parse($this->bid_opening_end_date)->format('Y-m-d H:i:s');
    }

    public function getTechnicalBidOpeningDateTimeAttribute() {
        if (!$this->technical_bid_opening_date) {
            return null;
        }
        return Carbon::parse($this->technical_bid_opening_date)->format('Y-m-d H:i:s');
    }

    public function getTechnicalBidClosingDateTimeAttribute() {
        if (!$this->technical_bid_closing_date) {
            return null;
        }
        return Carbon::parse($this->technical_bid_closing_date)->format('Y-m-d H:i:s');
    }


    public function getCommericalBidOpeningDateTimeAttribute() {
        if (!$this->commerical_bid_opening_date) {
            return null;
        }
        return Carbon::parse($this->commerical_bid_opening_date)->format('Y-m-d H:i:s');
    }

    public function getCommericalBidClosingDateTimeAttribute() {
        if (!$this->commerical_bid_closing_date) {
            return null;
        }
        return Carbon::parse($this->commerical_bid_closing_date)->format('Y-m-d H:i:s');
    }

    public static function getEditTenderMasterData($tenderMasterId, $companySystemID, $isTender){
        $tender = self::where('id', $tenderMasterId)
            ->whereNotNull('version_id')
            ->orderBy('amd_id', 'desc')
            ->first();

        if (!$tender) {
            return null;
        }

        $tender->load([
            'procument_activity' => function ($query) use ($tender) {
                $query->where('version_id', $tender->version_id)
                    ->where('is_deleted', 0);
            },
            'confirmed_by' => function ($query) {
                $query->select('employeeSystemID', 'empName');
            },
            'approvedRejectStatus' => function ($query) use ($companySystemID, $isTender) {
                $query->select('documentSystemCode', 'status')
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $isTender);
            }
        ]);

        return $tender;
    }

    public static function tenderMasterHistory($tenderMasterID, $versionID = 0){
        return self::where('id', $tenderMasterID)
            ->when($versionID > 0, function ($q) use ($versionID) {
                $q->where('version_id', $versionID);
            })
            ->whereNotNull('version_id')
            ->orderBy('amd_id', 'desc')
            ->first();
    }
    
}
