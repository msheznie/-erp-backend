<?php

namespace App\Repositories;

use App\Models\VatReturnFilledCategory;
use App\Repositories\BaseRepository;

/**
 * Class VatReturnFilledCategoryRepository
 * @package App\Repositories
 * @version September 10, 2021, 10:40 am +04
 *
 * @method VatReturnFilledCategory findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFilledCategory find($id, $columns = ['*'])
 * @method VatReturnFilledCategory first($columns = ['*'])
*/
class VatReturnFilledCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryID',
        'vatReturnFillingID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFilledCategory::class;
    }
}
