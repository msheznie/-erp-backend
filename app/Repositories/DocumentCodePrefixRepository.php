<?php

namespace App\Repositories;

use App\Models\DocumentCodePrefix;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodePrefixRepository
 * @package App\Repositories
 * @version March 6, 2025, 11:42 am +04
 *
 * @method DocumentCodePrefix findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodePrefix find($id, $columns = ['*'])
 * @method DocumentCodePrefix first($columns = ['*'])
*/
class DocumentCodePrefixRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type_based_id',
        'common_id',
        'description',
        'format',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodePrefix::class;
    }
}
