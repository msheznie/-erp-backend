<?php

namespace App\Repositories;

use App\Models\SupplierCritical;
use App\Repositories\BaseRepository;

/**
 * Class SupplierCriticalRepository
 * @package App\Repositories
 * @version March 2, 2018, 11:15 am UTC
 *
 * @method SupplierCritical findWithoutFail($id, $columns = ['*'])
 * @method SupplierCritical find($id, $columns = ['*'])
 * @method SupplierCritical first($columns = ['*'])
*/
class SupplierCriticalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCritical::class;
    }
}
