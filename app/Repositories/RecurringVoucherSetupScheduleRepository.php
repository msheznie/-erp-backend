<?php

namespace App\Repositories;

use App\Models\RecurringVoucherSetupSchedule;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RecurringVoucherSetupScheduleRepository
 * @package App\Repositories
 * @version February 7, 2024, 9:00 pm +04
 *
 * @method RecurringVoucherSetupSchedule findWithoutFail($id, $columns = ['*'])
 * @method RecurringVoucherSetupSchedule find($id, $columns = ['*'])
 * @method RecurringVoucherSetupSchedule first($columns = ['*'])
*/
class RecurringVoucherSetupScheduleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'recurringVoucherAutoId',
        'processDate',
        'currencyID',
        'amount',
        'jvGeneratedYN',
        'stopYN',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyFinanceYearID',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RecurringVoucherSetupSchedule::class;
    }
}
