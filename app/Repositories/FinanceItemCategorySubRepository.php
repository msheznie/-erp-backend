<?php

namespace App\Repositories;

use App\Models\FinanceItemCategorySub;
use App\Repositories\BaseRepository;

/**
 * Class FinanceItemCategorySubRepository
 * @package App\Repositories
 * @version March 8, 2018, 12:02 pm UTC
 *
 * @method FinanceItemCategorySub findWithoutFail($id, $columns = ['*'])
 * @method FinanceItemCategorySub find($id, $columns = ['*'])
 * @method FinanceItemCategorySub first($columns = ['*'])
*/
class FinanceItemCategorySubRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryDescription',
        'itemCategoryID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'createdDateTime',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'timeStamp',
        'enableSpecification'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinanceItemCategorySub::class;
    }
}
