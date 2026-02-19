<?php

namespace App\Repositories;

use App\Models\SrpErpFormCategory;
use App\Repositories\BaseRepository;

/**
 * Class SrpErpFormCategoryRepository
 * @package App\Repositories
 * @version September 3, 2021, 2:41 pm +04
 *
 * @method SrpErpFormCategory findWithoutFail($id, $columns = ['*'])
 * @method SrpErpFormCategory find($id, $columns = ['*'])
 * @method SrpErpFormCategory first($columns = ['*'])
*/
class SrpErpFormCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Category',
        'navigationMenuID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrpErpFormCategory::class;
    }
}
