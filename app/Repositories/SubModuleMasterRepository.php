<?php

namespace App\Repositories;

use App\Models\SubModuleMaster;
use App\Repositories\BaseRepository;

/**
 * Class SubModuleMasterRepository
 * @package App\Repositories
 * @version September 1, 2021, 1:59 pm +04
 *
 * @method SubModuleMaster findWithoutFail($id, $columns = ['*'])
 * @method SubModuleMaster find($id, $columns = ['*'])
 * @method SubModuleMaster first($columns = ['*'])
*/
class SubModuleMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'subModuleName',
        'moduleMasterID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SubModuleMaster::class;
    }
}
