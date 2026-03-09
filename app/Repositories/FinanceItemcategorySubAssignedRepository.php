<?php

namespace App\Repositories;

use App\Models\FinanceItemcategorySubAssigned;
use App\Repositories\BaseRepository;

/**
 * Class FinanceItemcategorySubAssignedRepository
 * @package App\Repositories
 * @version March 8, 2018, 12:03 pm UTC
 *
 * @method FinanceItemcategorySubAssigned findWithoutFail($id, $columns = ['*'])
 * @method FinanceItemcategorySubAssigned find($id, $columns = ['*'])
 * @method FinanceItemcategorySubAssigned first($columns = ['*'])
*/
class FinanceItemcategorySubAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mainItemCategoryID',
        'itemCategorySubID',
        'categoryDescription',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'companySystemID',
        'companyID',
        'isActive',
        'isAssigned',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinanceItemcategorySubAssigned::class;
    }
}
