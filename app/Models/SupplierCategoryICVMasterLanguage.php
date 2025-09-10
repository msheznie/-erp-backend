<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SupplierCategoryICVMasterLanguage",
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
 *          property="supCategoryICVMasterID",
 *          description="supCategoryICVMasterID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="categoryDescription",
 *          description="categoryDescription",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
class SupplierCategoryICVMasterLanguage extends Model
{

    public $table = 'suppliercategoryicvmaster_languages';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'supCategoryICVMasterID',
        'languageCode',
        'categoryDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supCategoryICVMasterID' => 'integer',
        'languageCode' => 'string',
        'categoryDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supCategoryICVMasterID' => 'required',
        'languageCode' => 'required',
        'categoryDescription' => 'required'
    ];

    public function supplierCategoryICVMaster()
    {
        return $this->belongsTo(SupplierCategoryICVMaster::class, 'supCategoryICVMasterID', 'supCategoryICVMasterID');
    }
}

