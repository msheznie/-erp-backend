<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePvApprovalTypeSetupAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $boolean = 'nullable|boolean';

        return [
            'setup_description' => 'sometimes|required|string|max:255',
            'supplier_payment' => $boolean,
            'supplier_advance_payment' => $boolean,
            'employee_payment' => $boolean,
            'employee_advance_payment' => $boolean,
            'direct_payment_general' => $boolean,
            'iou_voucher' => $boolean,
            'salary_transfer' => $boolean,
            'expense_claim' => $boolean,
            'petty_cash' => $boolean,
            'cash' => $boolean,
            'inter_company_funds_transfer' => $boolean,
            'collection_on_behalf' => $boolean,
            'inter_bank_account_transfer' => $boolean,
            'is_active' => $boolean,
        ];
    }
}

