<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CreatePvApprovalTypeSetupAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $binary = 'nullable|integer|in:0,1';

        return [
            'document_attachment_id' => 'required|integer',
            'company_system_id' => 'required|integer',
            'setup_description' => 'required|string|max:255',
            'is_amount_approval' => $binary,
            'is_general_approval' => $binary,
            'is_supplier_payment' => $binary,
            'is_supplier_advance_payment' => $binary,
            'is_employee_payment' => $binary,
            'is_employee_advance_payment' => $binary,
            'is_direct_payment_general' => $binary,
            'is_iou_voucher' => $binary,
            'is_salary_transfer' => $binary,
            'is_expense_claim' => $binary,
            'is_petty_cash' => $binary,
            'is_cash' => $binary,
            'is_inter_company_funds_transfer' => $binary,
            'is_collection_on_behalf' => $binary,
            'is_inter_bank_account_transfer' => $binary,
            'is_active' => $binary,
        ];
    }
}

