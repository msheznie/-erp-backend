<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportColumnTemplateDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="reportcolumntemplateDetailsID",
 *          description="reportcolumntemplateDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reportColumnTemplateID",
 *          description="reportColumnTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="columnID",
 *          description="columnID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ReportColumnTemplateDetail extends Model
{

    public $table = 'reportcolumntemplatedetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'reportColumnTemplateID',
        'columnID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'reportcolumntemplateDetailsID' => 'integer',
        'reportColumnTemplateID' => 'integer',
        'columnID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'reportcolumntemplateDetailsID' => 'required'
    ];

     public function column_data()
    {
        return $this->hasOne('App\Models\ReportTemplateColumns', 'columnID', 'columnID');
    }
}
