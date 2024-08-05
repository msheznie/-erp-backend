<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationTemplateSectionTableColumn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationTemplateSectionTableColumnRepository
 * @package App\Repositories
 * @version June 26, 2024, 11:41 am +04
 *
 * @method SupplierEvaluationTemplateSectionTableColumn findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationTemplateSectionTableColumn find($id, $columns = ['*'])
 * @method SupplierEvaluationTemplateSectionTableColumn first($columns = ['*'])
*/
class SupplierEvaluationTemplateSectionTableColumnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'table_id',
        'column_header',
        'column_type',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationTemplateSectionTableColumn::class;
    }
}
