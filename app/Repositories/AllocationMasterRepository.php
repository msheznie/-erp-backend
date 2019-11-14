<?php

namespace App\Repositories;

use App\Models\AllocationMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AllocationMasterRepository
 * @package App\Repositories
 * @version November 7, 2019, 2:09 pm +04
 *
 * @method AllocationMaster findWithoutFail($id, $columns = ['*'])
 * @method AllocationMaster find($id, $columns = ['*'])
 * @method AllocationMaster first($columns = ['*'])
*/
class AllocationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Desciption',
        'DesCode',
        'timesstamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AllocationMaster::class;
    }
}
