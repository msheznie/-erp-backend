<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SupplierEvaluationTemplateSectionTable",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supplier_evaluation_template_id",
 *          description="supplier_evaluation_template_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="table_name",
 *          description="table_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="table_row",
 *          description="table_row",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="table_column",
 *          description="table_column",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_by",
 *          description="created_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SupplierEvaluationTemplateSectionTable extends Model
{

    public $table = 'supplier_evaluation_template_section_table';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'supplier_evaluation_template_id',
        'evaluation_template_section_id',
        'table_name',
        'table_row',
        'table_column',
        'isConfirmed',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supplier_evaluation_template_id' => 'integer',
        'evaluation_template_section_id' => 'integer',
        'table_name' => 'string',
        'table_row' => 'integer',
        'table_column' => 'integer',
        'isConfirmed' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function column()
    {
        return $this->hasMany('App\Models\SupplierEvaluationTemplateSectionTableColumn', 'table_id', 'id');
    }

    public function row()
    {
        return $this->hasMany('App\Models\TemplateSectionTableRow', 'table_id', 'id');
    }

    public function formula()
    {
        return $this->hasMany('App\Models\EvaluationTemplateSectionFormula', 'table_id', 'id');
    }

    public function evaluationDetailRow()
    {
        return $this->hasMany('App\Models\SupplierEvaluationTableDetails', 'tableId', 'id');
    }
    
}
