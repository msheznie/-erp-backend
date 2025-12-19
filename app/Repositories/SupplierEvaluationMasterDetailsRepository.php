<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationMasterDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationMasterDetailsRepository
 * @package App\Repositories
 * @version June 5, 2024, 3:27 pm +04
 *
 * @method SupplierEvaluationMasterDetails findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationMasterDetails find($id, $columns = ['*'])
 * @method SupplierEvaluationMasterDetails first($columns = ['*'])
*/
class SupplierEvaluationMasterDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'master_id',
        'description',
        'score',
        'rating',
        'comment',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationMasterDetails::class;
    }
}
