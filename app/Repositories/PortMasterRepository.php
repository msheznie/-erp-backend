<?php

namespace App\Repositories;

use App\Models\PortMaster;
use App\Repositories\BaseRepository;

/**
 * Class PortMasterRepository
 * @package App\Repositories
 * @version May 23, 2022, 4:11 pm +04
 *
 * @method PortMaster findWithoutFail($id, $columns = ['*'])
 * @method PortMaster find($id, $columns = ['*'])
 * @method PortMaster first($columns = ['*'])
*/
class PortMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'port_name',
        'country_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PortMaster::class;
    }
}
