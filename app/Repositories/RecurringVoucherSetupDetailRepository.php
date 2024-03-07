<?php

namespace App\Repositories;

use App\Models\RecurringVoucherSetupDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RecurringVoucherSetupDetailRepository
 * @package App\Repositories
 * @version February 5, 2024, 8:42 am +04
 *
 * @method RecurringVoucherSetupDetail findWithoutFail($id, $columns = ['*'])
 * @method RecurringVoucherSetupDetail find($id, $columns = ['*'])
 * @method RecurringVoucherSetupDetail first($columns = ['*'])
*/
class RecurringVoucherSetupDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'recurringVoucherAutoId',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'chartOfAccountSystemID',
        'currencyID',
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
        return RecurringVoucherSetupDetail::class;
    }
}
