<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="RegisterSupplierBusinessCategoryAssign",
 *      required={""},
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supCategoryMasterID",
 *          description="supCategoryMasterID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
class RegisterSupplierBusinessCategoryAssign extends Model
{

    public $table = 'registersupplierbusinesscategoryassign';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'supCategoryMasterID',
        'supplierID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supCategoryMasterID' => 'integer',
        'supplierID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
