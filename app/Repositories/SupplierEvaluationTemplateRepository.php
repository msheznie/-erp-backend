<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationTemplateRepository
 * @package App\Repositories
 * @version June 12, 2024, 11:05 am +04
 *
 * @method SupplierEvaluationTemplate findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationTemplate find($id, $columns = ['*'])
 * @method SupplierEvaluationTemplate first($columns = ['*'])
*/
class SupplierEvaluationTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'template_name',
        'template_type',
        'is_active',
        'is_confirmed',
        'is_draft',
        'companySystemID',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationTemplate::class;
    }
}
