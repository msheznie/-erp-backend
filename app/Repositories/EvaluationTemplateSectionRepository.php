<?php

namespace App\Repositories;

use App\Models\EvaluationTemplateSection;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationTemplateSectionRepository
 * @package App\Repositories
 * @version June 28, 2024, 4:40 pm +04
 *
 * @method EvaluationTemplateSection findWithoutFail($id, $columns = ['*'])
 * @method EvaluationTemplateSection find($id, $columns = ['*'])
 * @method EvaluationTemplateSection first($columns = ['*'])
*/
class EvaluationTemplateSectionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplier_evaluation_template_id',
        'section_name',
        'section_type',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationTemplateSection::class;
    }
}
