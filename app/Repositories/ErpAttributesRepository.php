<?php

namespace App\Repositories;

use App\Models\ErpAttributes;
use App\Repositories\BaseRepository;

/**
 * Class ErpAttributesRepository
 * @package App\Repositories
 * @version October 26, 2021, 2:24 pm +04
 *
 * @method ErpAttributes findWithoutFail($id, $columns = ['*'])
 * @method ErpAttributes find($id, $columns = ['*'])
 * @method ErpAttributes first($columns = ['*'])
*/
class ErpAttributesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'field_type',
        'document_id',
        'document_master_id',
        'is_mendatory',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpAttributes::class;
    }
}
