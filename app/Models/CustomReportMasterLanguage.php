<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomReportMasterLanguage",
 *      required={"erpCustomReportMasterID", "languageCode", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="erpCustomReportMasterID",
 *          description="erpCustomReportMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
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
class CustomReportMasterLanguage extends Model
{

    public $table = 'erp_custom_report_master_translation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'erpCustomReportMasterID',
        'languageCode',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'erpCustomReportMasterID' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'erpCustomReportMasterID' => 'required|integer|exists:erp_custom_report_master,id',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the custom report master that owns the translation.
     */
    public function customReportMaster()
    {
        return $this->belongsTo(CustomReportMaster::class, 'erpCustomReportMasterID', 'id');
    }
}
