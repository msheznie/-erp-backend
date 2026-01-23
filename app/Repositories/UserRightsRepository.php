<?php

namespace App\Repositories;

use App\Models\UserRights;
use App\Repositories\BaseRepository;

/**
 * Class UserRightsRepository
 * @package App\Repositories
 * @version February 3, 2020, 2:31 pm +04
 *
 * @method UserRights findWithoutFail($id, $columns = ['*'])
 * @method UserRights find($id, $columns = ['*'])
 * @method UserRights first($columns = ['*'])
*/
class UserRightsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employeeID',
        'groupMasterID',
        'pageMasterID',
        'moduleMasterID',
        'companyID',
        'V',
        'A',
        'E',
        'D',
        'P',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserRights::class;
    }
}
