<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemClientReferenceNumberMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="itemClientReferenceNumberMasterID",
 *          description="itemClientReferenceNumberMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="idItemAssigned",
 *          description="idItemAssigned",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractUIID",
 *          description="contractUIID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientReferenceNumber",
 *          description="clientReferenceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserID",
 *          description="createdByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedByUserID",
 *          description="modifiedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string"
 *      )
 * )
 */
class ItemClientReferenceNumberMaster extends Model
{

    public $table = 'erp_itemclientreferencenumbermaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'idItemAssigned',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'companySystemID',
        'companyID',
        'customerID',
        'contractUIID',
        'contractID',
        'clientReferenceNumber',
        'createdByUserID',
        'createdDateTime',
        'modifiedByUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemClientReferenceNumberMasterID' => 'integer',
        'idItemAssigned' => 'integer',
        'itemSystemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'unitOfMeasure' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'customerID' => 'integer',
        'contractUIID' => 'integer',
        'contractID' => 'string',
        'clientReferenceNumber' => 'string',
        'createdByUserID' => 'string',
        'modifiedByUserID' => 'string',
        'modifiedDateTime' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
