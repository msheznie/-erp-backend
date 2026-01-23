<?php

namespace App\Repositories;

use App\Models\LeaveDataDetail;
use App\Repositories\BaseRepository;

/**
 * Class LeaveDataDetailRepository
 * @package App\Repositories
 * @version September 1, 2019, 3:54 pm +04
 *
 * @method LeaveDataDetail findWithoutFail($id, $columns = ['*'])
 * @method LeaveDataDetail find($id, $columns = ['*'])
 * @method LeaveDataDetail first($columns = ['*'])
*/
class LeaveDataDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'leavedatamasterID',
        'leavemasterID',
        'startDate',
        'endDate',
        'noOfWorkingDays',
        'noOfNonWorkingDays',
        'totalDays',
        'calculatedDays',
        'startLastHitchDate',
        'endLastHitchDate',
        'startFollowingHitchDate',
        'endFollowingHitchDate',
        'comment',
        'reportingMangerComment',
        'modifieduser',
        'modifiedpc',
        'createduserGroup',
        'createdpc',
        'timestamp',
        'claimedDays',
        'endFinalDate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveDataDetail::class;
    }
}
