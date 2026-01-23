<?php

namespace App\Repositories;

use App\Models\ChartOfAccountAllocationMaster;
use App\Repositories\BaseRepository;

/**
 * Class ChartOfAccountAllocationMasterRepository
 * @package App\Repositories
 * @version November 8, 2019, 12:55 pm +04
 *
 * @method ChartOfAccountAllocationMaster findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccountAllocationMaster find($id, $columns = ['*'])
 * @method ChartOfAccountAllocationMaster first($columns = ['*'])
*/
class ChartOfAccountAllocationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'companySystemID',
        'allocationmaid',
        'serviceLineCode',
        'serviceLineSystemID',
        'chartOfAccountCode',
        'chartOfAccountSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChartOfAccountAllocationMaster::class;
    }
}
