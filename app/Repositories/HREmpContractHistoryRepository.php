<?php

namespace App\Repositories;

use App\Models\HREmpContractHistory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HREmpContractHistoryRepository
 * @package App\Repositories
 * @version August 29, 2021, 4:40 pm +04
 *
 * @method HREmpContractHistory findWithoutFail($id, $columns = ['*'])
 * @method HREmpContractHistory find($id, $columns = ['*'])
 * @method HREmpContractHistory first($columns = ['*'])
*/
class HREmpContractHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'contactTypeID',
        'companyID',
        'contractStartDate',
        'contractEndDate',
        'contractRefNo',
        'isCurrent',
        'previousContractID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'ModifiedPC',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HREmpContractHistory::class;
    }
}
