<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="PrintTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_id",
 *          description="document_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="template_name",
 *          description="template_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="template_url",
 *          description="template_url",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="template_photo_url",
 *          description="template_photo_url",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_predefined",
 *          description="is_predefined",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="is_default",
 *          description="is_default",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
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
 *      ),
 *      @SWG\Property(
 *          property="deleted_at",
 *          description="deleted_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class PrintTemplate extends Model
{
    use SoftDeletes;

    public $table = 'print_templates';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'document_id',
        'template_name',
        'template_url',
        'editable_template_url',
        'template_photo_url',
        'is_predefined',
        'is_default',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'document_id' => 'integer',
        'template_name' => 'string',
        'template_url' => 'string',
        'editable_template_url' => 'string',
        'template_photo_url' => 'string',
        'is_predefined' => 'boolean',
        'is_default' => 'boolean',
        'status' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * get default template
     * @param $query
     * @return mixed
     */
    public function scopeDefaultTemplate($query)
    {
        return $query->where('is_default', true)
            ->whereNotNull('template_url')
            ->where('status', true);
    }

    /**
     * get parent's child of template properties
     * @return mixed
     */
    public function document()
    {
        return $this->belongsTo(DocumentMaster::class, 'document_id', 'documentSystemID');
    }
}
