<?php

namespace App\Repositories;

use App\Models\VatReturnFillingCategory;
use App\Repositories\BaseRepository;

/**
 * Class VatReturnFillingCategoryRepository
 * @package App\Repositories
 * @version September 10, 2021, 10:26 am +04
 *
 * @method VatReturnFillingCategory findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingCategory find($id, $columns = ['*'])
 * @method VatReturnFillingCategory first($columns = ['*'])
*/
class VatReturnFillingCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'category',
        'masterID',
        'isActive'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingCategory::class;
    }
}
