<?php

namespace App\Repositories;

use App\Models\POSMappingMaster;
use App\Repositories\BaseRepository;

/**
 * Class POSMappingMasterRepository
 * @package App\Repositories
 * @version July 18, 2022, 10:56 am +04
 *
 * @method POSMappingMaster findWithoutFail($id, $columns = ['*'])
 * @method POSMappingMaster find($id, $columns = ['*'])
 * @method POSMappingMaster first($columns = ['*'])
*/
class POSMappingMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'key'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSMappingMaster::class;
    }
}
