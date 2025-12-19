<?php

namespace App\Repositories;

use App\Models\BudgetControlLink;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetControlLinkRepository
 * @package App\Repositories
 * @version August 19, 2025, 3:58 pm +04
 *
 * @method BudgetControlLink findWithoutFail($id, $columns = ['*'])
 * @method BudgetControlLink find($id, $columns = ['*'])
 * @method BudgetControlLink first($columns = ['*'])
*/
class BudgetControlLinkRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'controlId',
        'createdPCID',
        'createdUserSystemID',
        'glAutoID',
        'glCode',
        'glDescription',
        'modifiedPCID',
        'modifiedUserSystemID',
        'sortOrder'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetControlLink::class;
    }
}
