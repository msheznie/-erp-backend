<?php

namespace App\Repositories;

use App\Models\ChartOfAccountAllocationDetail;
use App\Repositories\BaseRepository;

/**
 * Class ChartOfAccountAllocationDetailRepository
 * @package App\Repositories
 * @version November 8, 2019, 12:57 pm +04
 *
 * @method ChartOfAccountAllocationDetail findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccountAllocationDetail find($id, $columns = ['*'])
 * @method ChartOfAccountAllocationDetail first($columns = ['*'])
*/
class ChartOfAccountAllocationDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'chartOfAccountAllocationMasterID',
        'companyid',
        'companySystemID',
        'allocationmaid',
        'productLineCode',
        'productLineID',
        'percentage',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChartOfAccountAllocationDetail::class;
    }
}
