<?php

namespace App\Repositories;

use App\Models\DocumentCodeFormat;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodeFormatRepository
 * @package App\Repositories
 * @version January 30, 2025, 9:55 am +04
 *
 * @method DocumentCodeFormat findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodeFormat find($id, $columns = ['*'])
 * @method DocumentCodeFormat first($columns = ['*'])
*/
class DocumentCodeFormatRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'column_name',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodeFormat::class;
    }
}
