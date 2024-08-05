<?php

namespace App\Repositories;

use App\Models\EvaluationTemplateSectionLabel;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationTemplateSectionLabelRepository
 * @package App\Repositories
 * @version July 7, 2024, 1:14 pm +04
 *
 * @method EvaluationTemplateSectionLabel findWithoutFail($id, $columns = ['*'])
 * @method EvaluationTemplateSectionLabel find($id, $columns = ['*'])
 * @method EvaluationTemplateSectionLabel first($columns = ['*'])
*/
class EvaluationTemplateSectionLabelRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'evaluation_template_section_id',
        'labelName',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationTemplateSectionLabel::class;
    }
}
