<?php

namespace App\Repositories;

use App\Models\PeriodMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PeriodMasterRepository
 * @package App\Repositories
 * @version November 7, 2018, 10:03 am UTC
 *
 * @method PeriodMaster findWithoutFail($id, $columns = ['*'])
 * @method PeriodMaster find($id, $columns = ['*'])
 * @method PeriodMaster first($columns = ['*'])
*/
class PeriodMasterRepository extends BaseRepository
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
        return PeriodMaster::class;
    }
}
