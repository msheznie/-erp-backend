<?php

namespace App\Repositories;

use App\Models\POSTransStatus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSTransStatusRepository
 * @package App\Repositories
 * @version July 19, 2022, 12:37 pm +04
 *
 * @method POSTransStatus findWithoutFail($id, $columns = ['*'])
 * @method POSTransStatus find($id, $columns = ['*'])
 * @method POSTransStatus first($columns = ['*'])
*/
class POSTransStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSTransStatus::class;
    }
}
