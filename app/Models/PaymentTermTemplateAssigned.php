<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="PaymentTermTemplateAssigned",
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
 *          property="templateId",
 *          description="templateId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company",
 *          description="company",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="supplierCategory",
 *          description="supplierCategory",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="supplierId",
 *          description="supplierId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supplierName",
 *          description="supplierName",
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
class PaymentTermTemplateAssigned extends Model
{

    public $table = 'payment_term_template_assigned';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'templateID',
        'companySystemID',
        'supplierCategoryID',
        'supplierID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'templateID' => 'integer',
        'companySystemID' => 'integer',
        'supplierCategoryID' => 'integer',
        'supplierID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'templateID' => 'required',
        'companySystemID' => 'required',
        'supplierCategoryID' => 'required',
        'supplierID' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function template()
    {
        return $this->belongsTo(\App\Models\PaymentTermTemplate::class, 'templateID');
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\SupplierMaster::class, 'supplierID', 'supplierCodeSystem');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'companySystemID', 'companySystemID');
    }

    public function supplierCategory()
    {
        return $this->belongsTo(\App\Models\SupplierCategory::class, 'supplierCategoryID');
    }
}
