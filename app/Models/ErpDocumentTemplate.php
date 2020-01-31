<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ErpDocumentTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="documentTemplateID",
 *          description="documentTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="printTemplateID",
 *          description="printTemplateID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ErpDocumentTemplate extends Model
{

    public $table = 'erp_document_template';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'documentID',
        'companyID',
        'printTemplateID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentTemplateID' => 'integer',
        'documentID' => 'integer',
        'companyID' => 'integer',
        'printTemplateID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'documentTemplateID' => 'required'
    ];

    public function printTemplate()
    {
        return $this->hasOne('App\Models\ErpPrintTemplateMaster', 'printTemplateID', 'printTemplateID');
    }
}
