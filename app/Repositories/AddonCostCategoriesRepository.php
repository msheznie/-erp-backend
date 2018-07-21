<?php

namespace App\Repositories;

use App\Models\AddonCostCategories;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AddonCostCategoriesRepository
 * @package App\Repositories
 * @version July 20, 2018, 4:56 am UTC
 *
 * @method AddonCostCategories findWithoutFail($id, $columns = ['*'])
 * @method AddonCostCategories find($id, $columns = ['*'])
 * @method AddonCostCategories first($columns = ['*'])
*/
class AddonCostCategoriesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'costCatDes',
        'glCode',
        'timesStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AddonCostCategories::class;
    }
}
