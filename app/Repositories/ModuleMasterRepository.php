<?php

namespace App\Repositories;

use App\Models\ModuleMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ModuleMasterRepository
 * @package App\Repositories
 * @version September 1, 2021, 1:58 pm +04
 *
 * @method ModuleMaster findWithoutFail($id, $columns = ['*'])
 * @method ModuleMaster find($id, $columns = ['*'])
 * @method ModuleMaster first($columns = ['*'])
*/
class ModuleMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'moduleName'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ModuleMaster::class;
    }
}
