<?php

namespace App\Repositories;

use App\Models\SupplierImportance;
use App\Repositories\BaseRepository;

/**
 * Class SupplierImportanceRepository
 * @package App\Repositories
 * @version February 28, 2018, 4:16 am UTC
 *
 * @method SupplierImportance findWithoutFail($id, $columns = ['*'])
 * @method SupplierImportance find($id, $columns = ['*'])
 * @method SupplierImportance first($columns = ['*'])
*/
class SupplierImportanceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'importanceDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierImportance::class;
    }
}
