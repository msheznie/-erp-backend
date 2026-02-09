<?php

namespace App\Repositories;

use App\Models\HRMSPeriodMaster;
use App\Repositories\BaseRepository;

/**
 * Class HRMSPeriodMasterRepository
 * @package App\Repositories
 * @version November 18, 2019, 3:54 pm +04
 *
 * @method HRMSPeriodMaster findWithoutFail($id, $columns = ['*'])
 * @method HRMSPeriodMaster find($id, $columns = ['*'])
 * @method HRMSPeriodMaster first($columns = ['*'])
*/
class HRMSPeriodMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'periodMonth',
        'periodYear',
        'clientMonth',
        'clientStartDate',
        'clientEndDate',
        'noOfDays',
        'startDate',
        'endDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSPeriodMaster::class;
    }
}
