<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomReportColumns",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="report_master_id",
 *          description="report_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="label",
 *          description="label",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="column",
 *          description="column",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="column_type",
 *          description="column_type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sort_order",
 *          description="sort_order",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_sortabel",
 *          description="is_sortabel",
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
 *          property="is_default_sort",
 *          description="is_default_sort",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="is_default_group_by",
 *          description="is_default_group_by",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="is_filter",
 *          description="is_filter",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="filter_type",
 *          description="filter_type",
 *          type="integer",
 *          format="int32"
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
class CustomReportColumns extends Model
{

    public $table = 'erp_custom_report_columns';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'report_master_id',
        'label',
        'table',
        'column',
        'filter_column',
        'column_type',
        'sort_order',
        'is_sortabel',
        'sort_by',
        'is_group_by',
        'is_default_sort',
        'is_default_group_by',
        'is_filter',
        'filter_type',
        'is_relationship',
        'relationship',
        'relationship_columns'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'report_master_id' => 'integer',
        'label' => 'string',
        'table' => 'string',
        'column' => 'string',
        'filter_column' => 'string',
        'column_type' => 'integer',
        'sort_order' => 'integer',
        'is_sortabel' => 'boolean',
        'sort_by' => 'string',
        'is_group_by' => 'boolean',
        'is_default_sort' => 'boolean',
        'is_default_group_by' => 'boolean',
        'is_filter' => 'boolean',
        'filter_type' => 'integer',
        'is_relationship' => 'boolean',
        'relationship'  => 'string',
        'relationship_columns'  => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
