<?php

namespace App\Repositories;

use App\Models\RigMaster;
use App\Repositories\BaseRepository;

/**
 * Class RigMasterRepository
 * @package App\Repositories
 * @version August 15, 2018, 10:35 am UTC
 *
 * @method RigMaster findWithoutFail($id, $columns = ['*'])
 * @method RigMaster find($id, $columns = ['*'])
 * @method RigMaster first($columns = ['*'])
*/
class RigMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'RigDescription',
        'companyID',
        'oldID',
        'isRig'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RigMaster::class;
    }
}
