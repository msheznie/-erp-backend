<?php

namespace App\Repositories;

use App\Models\EvaluationTemplateSectionFormula;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationTemplateSectionFormulaRepository
 * @package App\Repositories
 * @version July 7, 2024, 1:16 pm +04
 *
 * @method EvaluationTemplateSectionFormula findWithoutFail($id, $columns = ['*'])
 * @method EvaluationTemplateSectionFormula find($id, $columns = ['*'])
 * @method EvaluationTemplateSectionFormula first($columns = ['*'])
*/
class EvaluationTemplateSectionFormulaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'evaluation_template_section_id',
        'table_id',
        'lable_id',
        'formulaType',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationTemplateSectionFormula::class;
    }
}
