<?php

namespace App\Repositories;

use App\Models\ExampleTableTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExampleTableTemplateRepository
 * @package App\Repositories
 * @version May 10, 2022, 10:31 am +04
 *
 * @method ExampleTableTemplate findWithoutFail($id, $columns = ['*'])
 * @method ExampleTableTemplate find($id, $columns = ['*'])
 * @method ExampleTableTemplate first($columns = ['*'])
*/
class ExampleTableTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'data'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExampleTableTemplate::class;
    }
}
