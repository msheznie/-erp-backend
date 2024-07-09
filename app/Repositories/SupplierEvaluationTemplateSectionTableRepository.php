<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationTemplateSectionTable;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationTemplateSectionTableRepository
 * @package App\Repositories
 * @version June 26, 2024, 11:40 am +04
 *
 * @method SupplierEvaluationTemplateSectionTable findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationTemplateSectionTable find($id, $columns = ['*'])
 * @method SupplierEvaluationTemplateSectionTable first($columns = ['*'])
*/
class SupplierEvaluationTemplateSectionTableRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplier_evaluation_template_id',
        'evaluation_template_section_id',
        'table_name',
        'table_row',
        'table_column',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationTemplateSectionTable::class;
    }
}
