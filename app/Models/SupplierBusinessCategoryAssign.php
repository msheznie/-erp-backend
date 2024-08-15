<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SupplierBusinessCategoryAssign",
 *      required={""},
 *      @OA\Property(
 *          property="supplierBusinessCategoryAssignID",
 *          description="supplierBusinessCategoryAssignID",
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
 *          property="supCategoryMasterID",
 *          description="supCategoryMasterID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SupplierBusinessCategoryAssign extends Model
{

    public $table = 'supplierbusinesscategoryassign';

    protected $primaryKey = 'supplierBusinessCategoryAssignID';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = null;

    public $fillable = [
        'supplierID',
        'supCategoryMasterID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierBusinessCategoryAssignID' => 'integer',
        'supplierID' => 'integer',
        'supCategoryMasterID' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supplierID' => 'required',
        'supCategoryMasterID' => 'required',
        'timestamp' => 'required'
    ];

    public function categoryMaster(){
        return $this->hasOne('App\Models\SupplierCategoryMaster', 'supCategoryMasterID','supCategoryMasterID');
    }
    
}
