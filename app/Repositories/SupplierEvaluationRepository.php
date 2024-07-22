<?php

namespace App\Repositories;

use App\Models\SupplierEvaluation;
use InfyOm\Generator\Common\BaseRepository;

class SupplierEvaluationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'evaluationSerialNo',
        'evaluationCode',
        'evaluationType',
        'evaluationTemplate',
        'documentCode',
        'documentId',
        'documentSystemCode',
        'supplierId',
        'supplierCode',
        'supplierName',
        'companySystemID',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluation::class;
    }
}
