<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="FinanceItemCategoryTypes",
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
 *          property="itemCategorySubID",
 *          description="itemCategorySubID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="categoryTypeID",
 *          description="categoryTypeID",
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
class FinanceItemCategoryTypes extends Model
{

    public $table = 'finance_item_category_types';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $with = ['category_type_master'];


    public $fillable = [
        'itemCategorySubID',
        'categoryTypeID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'itemCategorySubID' => 'integer',
        'categoryTypeID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'itemCategorySubID' => 'required',
        'categoryTypeID' => 'required'
    ];

    public function financeItemCategorySub()
    {
        return $this->belongsTo('App\Models\FinanceItemCategorySub', 'itemCategorySubID', 'itemCategorySubID');
    }

    public function category_type_master()
    {
        return $this->belongsTo('App\Models\ItemCategoryTypeMaster', 'categoryTypeID', 'id');
    }
}
