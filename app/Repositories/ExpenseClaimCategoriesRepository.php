<?php

namespace App\Repositories;

use App\Models\ExpenseClaimCategories;
use App\Repositories\BaseRepository;

/**
 * Class ExpenseClaimCategoriesRepository
 * @package App\Repositories
 * @version September 10, 2018, 6:06 am UTC
 *
 * @method ExpenseClaimCategories findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaimCategories find($id, $columns = ['*'])
 * @method ExpenseClaimCategories first($columns = ['*'])
*/
class ExpenseClaimCategoriesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'claimcategoriesDescription',
        'glCode',
        'glCodeDescription',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaimCategories::class;
    }
}
