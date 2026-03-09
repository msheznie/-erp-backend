<?php

namespace App\Repositories;

use App\Models\LogisticStatus;
use App\Repositories\BaseRepository;

/**
 * Class LogisticStatusRepository
 * @package App\Repositories
 * @version September 12, 2018, 5:10 am UTC
 *
 * @method LogisticStatus findWithoutFail($id, $columns = ['*'])
 * @method LogisticStatus find($id, $columns = ['*'])
 * @method LogisticStatus first($columns = ['*'])
*/
class LogisticStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'statusDescriptions',
        'createdUserID',
        'createdDateTime',
        'createdPCID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticStatus::class;
    }
}
