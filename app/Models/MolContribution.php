<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="MolContribution",
 *      required={""},
 *      @OA\Property(
 *          property="authority_id",
 *          description="authority_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="contribution_type",
 *          description="1.Labour Levy or 2.Social Contribution",
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
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="mol_calculation_type_id",
 *          description="1 - Before VAT",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="mol_expense_gl_account_id",
 *          description="mol_expense_gl_account_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="mol_percentage",
 *          description="mol_percentage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
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
class MolContribution extends Model
{

    public $table = 'mol_contribution';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'authority_id',
        'company_id',
        'contribution_type',
        'description',
        'mol_calculation_type_id',
        'mol_expense_gl_account_id',
        'mol_percentage',
        'status'
    ];

    protected $casts = [
        'authority_id' => 'integer',
        'company_id' => 'integer',
        'contribution_type' => 'integer',
        'description' => 'string',
        'id' => 'integer',
        'mol_calculation_type_id' => 'integer',
        'mol_expense_gl_account_id' => 'integer',
        'mol_percentage' => 'float',
        'status' => 'boolean'
    ];

    public static $rules = [
        'authority_id' => 'required',
        'company_id' => 'required',
        'contribution_type' => 'required',
        'description' => 'required',
        'mol_calculation_type_id' => 'required',
        'mol_expense_gl_account_id' => 'required'
    ];

    public function authority(){
        return $this->hasOne('App\Models\SupplierMaster', 'supplierCodeSystem', 'authority_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'companySystemID');
    }

    public function molExpenseGlAccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'mol_expense_gl_account_id', 'chartOfAccountSystemID');
    }
}
