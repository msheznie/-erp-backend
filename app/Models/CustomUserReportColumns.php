<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomUserReportColumns",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_report_id",
 *          description="user_report_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="column_id",
 *          description="column_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="label",
 *          description="label",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_sortable",
 *          description="is_sortable",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="sort_by",
 *          description="sort_by",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_group_by",
 *          description="is_group_by",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="is_filter",
 *          description="is_filter",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CustomUserReportColumns extends Model
{

    public $table = 'erp_custom_user_report_columns';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'user_report_id',
        'column_id',
        'label',
        'is_sort',
        'sort_by',
        'is_group_by',
        'is_filter',
        'sort_order'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_report_id' => 'integer',
        'column_id' => 'integer',
        'sort_order' => 'integer',
        'label' => 'string',
        'is_sort' => 'boolean',
        'sort_by' => 'string',
        'is_group_by' => 'boolean',
        'is_filter' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Get the translated label attribute
     *
     * @return string
     */
    public function getLabelAttribute($value)
    {
        $translationKey = str_replace(' ', '_', strtolower(trim($value)));
        
        $translatedValue = __("custom.{$translationKey}");
        
        if ($translatedValue === "custom.{$translationKey}") {
            return $value;
        }
        
        return $translatedValue;
    }

    public function column()
    {
        return $this->belongsTo(CustomReportColumns::class, 'column_id');
    }
}
