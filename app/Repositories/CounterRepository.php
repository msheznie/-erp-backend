<?php

namespace App\Repositories;

use App\Models\Counter;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CounterRepository
 * @package App\Repositories
 * @version January 7, 2019, 12:22 pm +04
 *
 * @method Counter findWithoutFail($id, $columns = ['*'])
 * @method Counter find($id, $columns = ['*'])
 * @method Counter first($columns = ['*'])
*/
class CounterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'counterCode',
        'counterName',
        'isActive',
        'wareHouseID',
        'companySystemID',
        'companyID',
        'createdPCID',
        'createdUserID',
        'createdUserSystemID',
        'createdUserName',
        'createdUserGroup',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Counter::class;
    }
}
