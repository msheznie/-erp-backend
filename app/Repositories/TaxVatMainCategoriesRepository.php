<?php

namespace App\Repositories;

use App\Models\TaxVatMainCategories;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxVatMainCategoriesRepository
 * @package App\Repositories
 * @version August 4, 2020, 1:52 pm +04
 *
 * @method TaxVatMainCategories findWithoutFail($id, $columns = ['*'])
 * @method TaxVatMainCategories find($id, $columns = ['*'])
 * @method TaxVatMainCategories first($columns = ['*'])
*/
class TaxVatMainCategoriesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'taxMasterAutoID',
        'mainCategoryDescription',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxVatMainCategories::class;
    }
}
