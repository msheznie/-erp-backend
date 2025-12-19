<?php

namespace App\Repositories;

use App\Models\SupplierEvaluationTemplateComment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierEvaluationTemplateCommentRepository
 * @package App\Repositories
 * @version June 23, 2024, 10:39 am +04
 *
 * @method SupplierEvaluationTemplateComment findWithoutFail($id, $columns = ['*'])
 * @method SupplierEvaluationTemplateComment find($id, $columns = ['*'])
 * @method SupplierEvaluationTemplateComment first($columns = ['*'])
*/
class SupplierEvaluationTemplateCommentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplier_evaluation_template_id',
        'label',
        'comment',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluationTemplateComment::class;
    }
}
