<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="FinalReturnIncomeTemplateDefaults",
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
 *          property="line_no",
 *          description="line_no",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="type",
 *          description="1 = Addition, 2 = Deduction, 3 = Total",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="appendix",
 *          description="appendix",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="sectionType",
 *          description="1 = Raw, 2 = Group Total",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
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
class FinalReturnIncomeTemplateDefaults extends Model
{

    public $table = 'final_return_income_template_defaults';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'line_no',
        'type',
        'description',
        'appendix',
        'sectionType'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'line_no' => 'integer',
        'type' => 'boolean',
        'description' => 'string',
        'appendix' => 'string',
        'sectionType' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'type' => 'required',
        'description' => 'required',
        'sectionType' => 'required'
    ];

    public function links()
    {
        return $this->hasMany(FinalReturnIncomeTemplateLinks::class, 'rawId', 'id');
    }
}
