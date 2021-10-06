<?php

namespace App\Repositories;

use App\Models\VatSubCategoryType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VatSubCategoryTypeRepository
 * @package App\Repositories
 * @version September 14, 2021, 7:59 am +04
 *
 * @method VatSubCategoryType findWithoutFail($id, $columns = ['*'])
 * @method VatSubCategoryType find($id, $columns = ['*'])
 * @method VatSubCategoryType first($columns = ['*'])
*/
class VatSubCategoryTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatSubCategoryType::class;
    }
}
