<?php

namespace App\Repositories;

use App\Models\SupplierType;
use App\Repositories\BaseRepository;

/**
 * Class SupplierTypeRepository
 * @package App\Repositories
 * @version February 28, 2018, 4:18 am UTC
 *
 * @method SupplierType findWithoutFail($id, $columns = ['*'])
 * @method SupplierType find($id, $columns = ['*'])
 * @method SupplierType first($columns = ['*'])
*/
class SupplierTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierType::class;
    }
}
