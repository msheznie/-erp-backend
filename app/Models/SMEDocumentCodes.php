<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMEDocumentCodes",
 *      required={""},
 *      @SWG\Property(
 *          property="documentAutoID",
 *          description="documentAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="document",
 *          description="document",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isApprovalDocument",
 *          description="1- yes 2- No",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isFinance",
 *          description="isFinance",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="moduleID",
 *          description="moduleID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="icon",
 *          description="icon",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentTable",
 *          description="documentTable",
 *          type="string"
 *      )
 * )
 */
class SMEDocumentCodes extends Model
{

    public $table = 'srp_erp_documentcodes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentID',
        'document',
        'isApprovalDocument',
        'isFinance',
        'moduleID',
        'icon',
        'documentTable'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentAutoID' => 'integer',
        'documentID' => 'string',
        'document' => 'string',
        'isApprovalDocument' => 'integer',
        'isFinance' => 'integer',
        'moduleID' => 'integer',
        'icon' => 'string',
        'documentTable' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
