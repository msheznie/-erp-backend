<?php

namespace App\Repositories;

use App\Models\PurchaseOrderCategory;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseOrderCategoryRepository
 * @package App\Repositories
 * @version May 30, 2018, 8:53 am UTC
 *
 * @method PurchaseOrderCategory findWithoutFail($id, $columns = ['*'])
 * @method PurchaseOrderCategory find($id, $columns = ['*'])
 * @method PurchaseOrderCategory first($columns = ['*'])
*/
class PurchaseOrderCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseOrderCategory::class;
    }
}
