<?php

namespace App\Repositories;

use App\Models\HRMSLeaveAccrualPolicyType;
use App\Repositories\BaseRepository;

/**
 * Class HRMSLeaveAccrualPolicyTypeRepository
 * @package App\Repositories
 * @version November 25, 2019, 10:03 am +04
 *
 * @method HRMSLeaveAccrualPolicyType findWithoutFail($id, $columns = ['*'])
 * @method HRMSLeaveAccrualPolicyType find($id, $columns = ['*'])
 * @method HRMSLeaveAccrualPolicyType first($columns = ['*'])
*/
class HRMSLeaveAccrualPolicyTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'isOnlyFemale',
        'isOnlyMuslim',
        'isExpat',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSLeaveAccrualPolicyType::class;
    }
}
