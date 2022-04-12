<?php

namespace App\Repositories;

use App\Models\TenderType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderTypeRepository
 * @package App\Repositories
 * @version March 10, 2022, 2:09 pm +04
 *
 * @method TenderType findWithoutFail($id, $columns = ['*'])
 * @method TenderType find($id, $columns = ['*'])
 * @method TenderType first($columns = ['*'])
*/
class TenderTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderType::class;
    }
}
