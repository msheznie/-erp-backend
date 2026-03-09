<?php

namespace App\Repositories;

use App\Models\POSMappingDetail;
use App\Repositories\BaseRepository;

/**
 * Class POSMappingDetailRepository
 * @package App\Repositories
 * @version July 18, 2022, 10:56 am +04
 *
 * @method POSMappingDetail findWithoutFail($id, $columns = ['*'])
 * @method POSMappingDetail find($id, $columns = ['*'])
 * @method POSMappingDetail first($columns = ['*'])
*/
class POSMappingDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'master_id',
        'table',
        'key'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSMappingDetail::class;
    }
}
