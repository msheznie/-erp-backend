<?php

namespace App\Repositories;

use App\Models\RecurringVoucherSetupScheDet;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RecurringVoucherSetupScheDetRepository
 * @package App\Repositories
 * @version May 18, 2025, 1:19 pm +04
 *
 * @method RecurringVoucherSetupScheDet findWithoutFail($id, $columns = ['*'])
 * @method RecurringVoucherSetupScheDet find($id, $columns = ['*'])
 * @method RecurringVoucherSetupScheDet first($columns = ['*'])
*/
class RecurringVoucherSetupScheDetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'recurringVoucherAutoId',
        'recurringVoucherSheduleAutoId',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'chartOfAccountSystemID',
        'currencyID',
        'detailProjectID',
        'companyID',
        'glAccount',
        'glAccountDescription',
        'comments',
        'debitAmount',
        'creditAmount',
        'serviceLineSystemID',
        'serviceLineCode',
        'contractUID',
        'clientContractID',
        'isChecked',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RecurringVoucherSetupScheDet::class;
    }
}
