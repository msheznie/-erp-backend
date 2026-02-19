<?php

namespace App\Repositories;

use App\Models\ErpAttributesFieldType;
use App\Repositories\BaseRepository;

/**
 * Class ErpAttributesFieldTypeRepository
 * @package App\Repositories
 * @version October 26, 2021, 3:23 pm +04
 *
 * @method ErpAttributesFieldType findWithoutFail($id, $columns = ['*'])
 * @method ErpAttributesFieldType find($id, $columns = ['*'])
 * @method ErpAttributesFieldType first($columns = ['*'])
*/
class ErpAttributesFieldTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpAttributesFieldType::class;
    }
}
