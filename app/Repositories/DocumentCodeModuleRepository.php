<?php

namespace App\Repositories;

use App\Models\DocumentCodeModule;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodeModuleRepository
 * @package App\Repositories
 * @version January 29, 2025, 4:46 pm +04
 *
 * @method DocumentCodeModule findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodeModule find($id, $columns = ['*'])
 * @method DocumentCodeModule first($columns = ['*'])
*/
class DocumentCodeModuleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'module_name',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodeModule::class;
    }
}
