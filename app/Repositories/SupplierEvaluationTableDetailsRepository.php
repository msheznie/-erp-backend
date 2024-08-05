<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationTableDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationTableDetailsRepository
 * @package App\Repositories
 * @version July 19, 2024, 12:12 pm +04
 *
 * @method SupplierEvaluationTableDetails findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationTableDetails find($id, $columns = ['*'])
 * @method SupplierEvaluationTableDetails first($columns = ['*'])
*/
class SupplierEvaluationTableDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'evaluationId',
        'tableId',
        'rowData',
        'createdBy',
        'updatedBy',
        'createdAt',
        'updatedAt'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationTableDetails::class;
    }
}
