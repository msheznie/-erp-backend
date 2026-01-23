<?php

namespace App\Repositories;

use App\Models\PoCategory;
use App\Repositories\BaseRepository;

/**
 * Class PoCategoryRepository
 * @package App\Repositories
 * @version November 16, 2021, 12:10 pm +04
 *
 * @method PoCategory findWithoutFail($id, $columns = ['*'])
 * @method PoCategory find($id, $columns = ['*'])
 * @method PoCategory first($columns = ['*'])
*/
class PoCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'isActive',
        'isDefault',
        'createdDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoCategory::class;
    }
}
