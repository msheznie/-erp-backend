<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRDocumentDescriptionMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="DocDesID",
 *          description="DocDesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DocDescription",
 *          description="DocDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="systemTypeID",
 *          description="FK => srp_erp_system_document_types.id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SchMasterID",
 *          description="SchMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BranchID",
 *          description="BranchID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Erp_companyID",
 *          description="Erp_companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDeleted",
 *          description="isDeleted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CreatedUserName",
 *          description="CreatedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CreatedDate",
 *          description="CreatedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="CreatedPC",
 *          description="CreatedPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedUserName",
 *          description="ModifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Timestamp",
 *          description="Timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedPC",
 *          description="ModifiedPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SortOrder",
 *          description="SortOrder",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class HRDocumentDescriptionMaster extends Model
{

    public $table = 'srp_documentdescriptionmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'DocDescription',
        'systemTypeID',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'isDeleted',
        'CreatedUserName',
        'createdUserID',
        'CreatedDate',
        'CreatedPC',
        'modifiedUserID',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC',
        'SortOrder'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'DocDesID' => 'integer',
        'DocDescription' => 'string',
        'systemTypeID' => 'integer',
        'SchMasterID' => 'integer',
        'BranchID' => 'integer',
        'Erp_companyID' => 'integer',
        'isDeleted' => 'integer',
        'CreatedUserName' => 'string',
        'createdUserID' => 'integer',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'modifiedUserID' => 'integer',
        'ModifiedUserName' => 'string',
        'Timestamp' => 'datetime',
        'ModifiedPC' => 'string',
        'SortOrder' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
