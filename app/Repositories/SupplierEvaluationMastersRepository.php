<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationMasters;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationMastersRepository
 * @package App\Repositories
 * @version June 5, 2024, 3:24 pm +04
 *
 * @method SupplierEvaluationMasters findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationMasters find($id, $columns = ['*'])
 * @method SupplierEvaluationMasters first($columns = ['*'])
*/
class SupplierEvaluationMastersRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'type',
        'is_active',
        'companySystemID',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationMasters::class;
    }
}
