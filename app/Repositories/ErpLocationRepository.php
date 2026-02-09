<?php

namespace App\Repositories;

use App\Models\ErpLocation;
use App\Repositories\BaseRepository;

/**
 * Class ErpLocationRepository
 * @package App\Repositories
 * @version March 15, 2018, 9:41 am UTC
 *
 * @method ErpLocation findWithoutFail($id, $columns = ['*'])
 * @method ErpLocation find($id, $columns = ['*'])
 * @method ErpLocation first($columns = ['*'])
*/
class ErpLocationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'locationName'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpLocation::class;
    }
}
