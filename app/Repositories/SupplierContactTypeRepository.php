<?php

namespace App\Repositories;

use App\Models\SupplierContactType;
use App\Repositories\BaseRepository;

/**
 * Class SupplierContactTypeRepository
 * @package App\Repositories
 * @version March 6, 2018, 10:53 am UTC
 *
 * @method SupplierContactType findWithoutFail($id, $columns = ['*'])
 * @method SupplierContactType find($id, $columns = ['*'])
 * @method SupplierContactType first($columns = ['*'])
*/
class SupplierContactTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierContactDescription',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierContactType::class;
    }
}
