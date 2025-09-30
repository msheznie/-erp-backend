<?php
/**
 * =============================================
 * -- File Name : ReportTemplateNumbers.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 29- January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateNumbers",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="string",
 *          format="String"
 *      )
 * )
 */
class ReportTemplateNumbers extends Model
{

    public $table = 'erp_companyreporttemplatenumbers';
    
    const CREATED_AT = 'timesStamp';
    const UPDATED_AT = 'timesStamp';

    public $fillable = [
        'value',
        'timesStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function getValueAttribute($value)
    {
        if ($value == "Default") {
            return trans('custom.default');
        } else {
            return $this->attributes['value'];
        }
    }
}
