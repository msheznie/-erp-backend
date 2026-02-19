<?php

namespace App\Repositories;

use App\Models\LogisticShippingStatus;
use App\Repositories\BaseRepository;

/**
 * Class LogisticShippingStatusRepository
 * @package App\Repositories
 * @version September 12, 2018, 5:09 am UTC
 *
 * @method LogisticShippingStatus findWithoutFail($id, $columns = ['*'])
 * @method LogisticShippingStatus find($id, $columns = ['*'])
 * @method LogisticShippingStatus first($columns = ['*'])
*/
class LogisticShippingStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'logisticMasterID',
        'shippingStatusID',
        'statusDate',
        'statusComment',
        'createdUserID',
        'createdPCID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticShippingStatus::class;
    }
}
