<?php

namespace App\Repositories;

use App\Models\ErpAttributesDropdown;
use App\Repositories\BaseRepository;

/**
 * Class ErpAttributesDropdownRepository
 * @package App\Repositories
 * @version October 26, 2021, 2:27 pm +04
 *
 * @method ErpAttributesDropdown findWithoutFail($id, $columns = ['*'])
 * @method ErpAttributesDropdown find($id, $columns = ['*'])
 * @method ErpAttributesDropdown first($columns = ['*'])
*/
class ErpAttributesDropdownRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'attributes_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpAttributesDropdown::class;
    }
}
