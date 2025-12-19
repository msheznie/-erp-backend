<?php

namespace App\Repositories;

use App\Models\ExpenseClaimType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExpenseClaimTypeRepository
 * @package App\Repositories
 * @version September 10, 2018, 6:06 am UTC
 *
 * @method ExpenseClaimType findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaimType find($id, $columns = ['*'])
 * @method ExpenseClaimType first($columns = ['*'])
*/
class ExpenseClaimTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'expenseClaimTypeDescription',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaimType::class;
    }
}
