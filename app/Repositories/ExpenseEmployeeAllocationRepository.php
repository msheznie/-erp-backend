<?php

namespace App\Repositories;

use App\Models\ExpenseEmployeeAllocation;
use App\Repositories\BaseRepository;

/**
 * Class ExpenseEmployeeAllocationRepository
 * @package App\Repositories
 * @version March 8, 2022, 9:58 am +04
 *
 * @method ExpenseEmployeeAllocation findWithoutFail($id, $columns = ['*'])
 * @method ExpenseEmployeeAllocation find($id, $columns = ['*'])
 * @method ExpenseEmployeeAllocation first($columns = ['*'])
*/
class ExpenseEmployeeAllocationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employeeSystemID',
        'documentSystemID',
        'documentDetailID',
        'chartOfAccountSystemID',
        'documentSystemCode',
        'amount',
        'amountRpt',
        'amountLocal',
        'dateOfDeduction',
        'assignedQty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseEmployeeAllocation::class;
    }
}
