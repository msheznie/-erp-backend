<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FieldMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="FieldID",
 *          description="FieldID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fieldShortCode",
 *          description="fieldShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fieldName",
 *          description="fieldName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyId",
 *          description="companyId",
 *          type="string"
 *      )
 * )
 */
class FieldMaster extends Model
{

    public $table = 'fieldmaster';
    
   /* const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/
    protected $primaryKey  = 'FieldID';


    public $fillable = [
        'fieldShortCode',
        'fieldName',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'companyId'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'FieldID' => 'integer',
        'fieldShortCode' => 'string',
        'fieldName' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'companyId' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
