<?php

namespace App\Repositories;

use App\Models\ChartOfAccountAllocationDetailHistory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChartOfAccountAllocationDetailHistoryRepository
 * @package App\Repositories
 * @version February 25, 2020, 2:49 pm +04
 *
 * @method ChartOfAccountAllocationDetailHistory findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccountAllocationDetailHistory find($id, $columns = ['*'])
 * @method ChartOfAccountAllocationDetailHistory first($columns = ['*'])
*/
class ChartOfAccountAllocationDetailHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'jvMasterAutoId',
        'timestamp',
        'percentage',
        'productLineID',
        'productLineCode',
        'allocationmaid',
        'companySystemID',
        'companyid',
        'chartOfAccountAllocationMasterID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChartOfAccountAllocationDetailHistory::class;
    }
}
