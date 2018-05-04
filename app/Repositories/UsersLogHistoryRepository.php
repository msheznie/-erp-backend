<?php

namespace App\Repositories;

use App\Models\UsersLogHistory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UsersLogHistoryRepository
 * @package App\Repositories
 * @version May 1, 2018, 9:29 am UTC
 *
 * @method UsersLogHistory findWithoutFail($id, $columns = ['*'])
 * @method UsersLogHistory find($id, $columns = ['*'])
 * @method UsersLogHistory first($columns = ['*'])
*/
class UsersLogHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'empID',
        'loginPCId'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UsersLogHistory::class;
    }
}
