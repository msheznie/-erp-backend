<?php

namespace App\Models;

use Eloquent as Model;

class PvApprovalTypeSetup extends Model
{
    public $table = 'pv_approval_type_setup';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'document_attachment_id',
        'company_system_id',
        'setup_description',
        'is_amount_approval',
        'is_general_approval',
        'is_supplier_payment',
        'is_supplier_advance_payment',
        'is_employee_payment',
        'is_employee_advance_payment',
        'is_direct_payment_general',
        'is_iou_voucher',
        'is_salary_transfer',
        'is_expense_claim',
        'is_petty_cash',
        'is_cash',
        'is_inter_company_funds_transfer',
        'is_collection_on_behalf',
        'is_inter_bank_account_transfer',
        'is_refund',
        'is_active',
    ];

    protected $casts = [
        'id' => 'integer',
        'document_attachment_id' => 'integer',
        'company_system_id' => 'integer',
        'setup_description' => 'string',
        'is_amount_approval' => 'integer',
        'is_general_approval' => 'integer',
        'is_supplier_payment' => 'integer',
        'is_supplier_advance_payment' => 'integer',
        'is_employee_payment' => 'integer',
        'is_employee_advance_payment' => 'integer',
        'is_direct_payment_general' => 'integer',
        'is_iou_voucher' => 'integer',
        'is_salary_transfer' => 'integer',
        'is_expense_claim' => 'integer',
        'is_petty_cash' => 'integer',
        'is_cash' => 'integer',
        'is_inter_company_funds_transfer' => 'integer',
        'is_collection_on_behalf' => 'integer',
        'is_inter_bank_account_transfer' => 'integer',
        'is_refund' => 'integer',
        'is_active' => 'integer',
    ];

    public const TYPE_FINGERPRINT_COLUMNS = [
        'is_amount_approval',
        'is_general_approval',
        'is_supplier_payment',
        'is_supplier_advance_payment',
        'is_employee_payment',
        'is_employee_advance_payment',
        'is_direct_payment_general',
        'is_iou_voucher',
        'is_salary_transfer',
        'is_expense_claim',
        'is_petty_cash',
        'is_cash',
        'is_inter_company_funds_transfer',
        'is_collection_on_behalf',
        'is_inter_bank_account_transfer',
        'is_refund',
    ];
}

