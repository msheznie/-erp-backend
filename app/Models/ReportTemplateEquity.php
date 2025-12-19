<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="ReportTemplateEquity",
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
 *          property="templateMasterID",
 *          description="templateMasterID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="sort_order",
 *          description="sort_order",
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
class ReportTemplateEquity extends Model
{

    public $table = 'erp_report_template_equity';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'templateMasterID',
        'description',
        'sort_order',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'templateMasterID' => 'integer',
        'description' => 'string',
        'sort_order' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'description' => 'required',
        'sort_order' => 'required'
    ];

    public function gllink()
    {
        return $this->hasMany('App\Models\ReportTemplateLinks','templateDetailID','id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $query = self::where('templateMasterID', $model->templateMasterID)
                         ->where('companySystemID', $model->companySystemID)
                         ->where('sort_order', $model->sort_order);

            if ($model->exists) {
                $query->where('id', '!=', $model->id);
            }

            if ($query->exists()) {
                 throw new \Exception("This sort order already exists, please select a different one");

            }

            $queryDescription = self::where('templateMasterID', $model->templateMasterID)
            ->where('companySystemID', $model->companySystemID)
            ->where('description', $model->description);

                if ($model->exists) {
                $queryDescription->where('id', '!=', $model->id);
                }

            if ($queryDescription->exists()) {
                 throw new \Exception("This description already exists, please select a different one.");
            }
        });
    }

  
}
