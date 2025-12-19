<?php

namespace App\Repositories;

use App\Models\DocumentCodeTypeBased;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodeTypeBasedRepository
 * @package App\Repositories
 * @version January 30, 2025, 9:27 am +04
 *
 * @method DocumentCodeTypeBased findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodeTypeBased find($id, $columns = ['*'])
 * @method DocumentCodeTypeBased first($columns = ['*'])
*/
class DocumentCodeTypeBasedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_transaction_id',
        'type_name',
        'master_prefix',
        'type_prefix',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodeTypeBased::class;
    }
}
