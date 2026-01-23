<?php

namespace App\Repositories;

use App\Models\DeliveryTermsMaster;
use App\Repositories\BaseRepository;

/**
 * Class DeliveryTermsMasterRepository
 * @package App\Repositories
 * @version May 23, 2022, 4:16 pm +04
 *
 * @method DeliveryTermsMaster findWithoutFail($id, $columns = ['*'])
 * @method DeliveryTermsMaster find($id, $columns = ['*'])
 * @method DeliveryTermsMaster first($columns = ['*'])
*/
class DeliveryTermsMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryTermsMaster::class;
    }
}
