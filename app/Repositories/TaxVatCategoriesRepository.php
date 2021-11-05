<?php

namespace App\Repositories;

use App\Models\TaxVatCategories;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxVatCategoriesRepository
 * @package App\Repositories
 * @version July 31, 2020, 2:56 pm +04
 *
 * @method TaxVatCategories findWithoutFail($id, $columns = ['*'])
 * @method TaxVatCategories find($id, $columns = ['*'])
 * @method TaxVatCategories first($columns = ['*'])
*/
class TaxVatCategoriesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'taxMasterAutoID',
        'mainCategory',
        'subCategory',
        'percentage',
        'applicableOn',
        'createdPCID',
        'createdUserID',
        'createdUserSystemID',
        'createdDateTime',
        'subCatgeoryType',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxVatCategories::class;
    }
}
