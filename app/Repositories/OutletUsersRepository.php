<?php

namespace App\Repositories;

use App\Models\OutletUsers;
use App\Repositories\BaseRepository;

/**
 * Class OutletUsersRepository
 * @package App\Repositories
 * @version January 3, 2019, 9:10 am +04
 *
 * @method OutletUsers findWithoutFail($id, $columns = ['*'])
 * @method OutletUsers find($id, $columns = ['*'])
 * @method OutletUsers first($columns = ['*'])
*/
class OutletUsersRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'userID',
        'wareHouseID',
        'counterID',
        'isActive',
        'companySystemID',
        'companyID',
        'createdPCID',
        'createdUserSystemID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
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
        return OutletUsers::class;
    }
}
