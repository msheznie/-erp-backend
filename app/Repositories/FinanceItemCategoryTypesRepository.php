<?php

namespace App\Repositories;

use App\Models\FinanceItemCategoryTypes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinanceItemCategoryTypesRepository
 * @package App\Repositories
 * @version August 16, 2024, 4:19 pm +04
 *
 * @method FinanceItemCategoryTypes findWithoutFail($id, $columns = ['*'])
 * @method FinanceItemCategoryTypes find($id, $columns = ['*'])
 * @method FinanceItemCategoryTypes first($columns = ['*'])
*/
class FinanceItemCategoryTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemCategorySubID',
        'categoryTypeID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinanceItemCategoryTypes::class;
    }
}
