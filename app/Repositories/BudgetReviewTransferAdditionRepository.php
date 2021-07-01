<?php

namespace App\Repositories;

use App\Models\BudgetReviewTransferAddition;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetReviewTransferAdditionRepository
 * @package App\Repositories
 * @version June 30, 2021, 2:19 pm +04
 *
 * @method BudgetReviewTransferAddition findWithoutFail($id, $columns = ['*'])
 * @method BudgetReviewTransferAddition find($id, $columns = ['*'])
 * @method BudgetReviewTransferAddition first($columns = ['*'])
*/
class BudgetReviewTransferAdditionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budgetTransferAdditionID',
        'budgetTransferType',
        'documentSystemCode',
        'documentSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetReviewTransferAddition::class;
    }
}
