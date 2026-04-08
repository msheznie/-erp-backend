<?php

namespace App\Repositories;

use App\Models\PvApprovalTypeSetup;
use App\Repositories\BaseRepository;

/**
 * Class PvApprovalTypeSetupRepository
 *
 * @method PvApprovalTypeSetup findWithoutFail($id, $columns = ['*'])
 * @method PvApprovalTypeSetup find($id, $columns = ['*'])
 * @method PvApprovalTypeSetup first($columns = ['*'])
 */
class PvApprovalTypeSetupRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'setup_description',
        'supplier_payment',
        'supplier_advance_payment',
        'employee_payment',
        'employee_advance_payment',
        'direct_payment_general',
        'iou_voucher',
        'salary_transfer',
        'expense_claim',
        'petty_cash',
        'cash',
        'inter_company_funds_transfer',
        'collection_on_behalf',
        'inter_bank_account_transfer',
        'is_active',
    ];

    public function model()
    {
        return PvApprovalTypeSetup::class;
    }
}

