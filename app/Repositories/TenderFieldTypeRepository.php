<?php

namespace App\Repositories;

use App\Models\TenderFieldType;
use App\Repositories\BaseRepository;

/**
 * Class TenderFieldTypeRepository
 * @package App\Repositories
 * @version March 7, 2022, 2:09 pm +04
 *
 * @method TenderFieldType findWithoutFail($id, $columns = ['*'])
 * @method TenderFieldType find($id, $columns = ['*'])
 * @method TenderFieldType first($columns = ['*'])
*/
class TenderFieldTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderFieldType::class;
    }
}
