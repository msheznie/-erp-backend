<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportColumnTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="reportColumnTemplateID",
 *          description="reportColumnTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateName",
 *          description="templateName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateImage",
 *          description="templateImage",
 *          type="string"
 *      )
 * )
 */
class ReportColumnTemplate extends Model
{

    public $table = 'reportcolumntemplate';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $primaryKey = 'reportColumnTemplateID';
    protected $appends = ['template_url'];

    public $fillable = [
        'templateName',
        'templateImage'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'reportColumnTemplateID' => 'integer',
        'templateName' => 'string',
        'templateImage' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function getTemplateUrlAttribute(){
        return asset($this->templateImage);
    }
}
