<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrmsDesignation",
 *      required={""},
 *      @SWG\Property(
 *          property="DesignationID",
 *          description="DesignationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DesDescription",
 *          description="DesDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isRequiredSelection",
 *          description="isRequiredSelection",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SelectionID",
 *          description="SelectionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DesDashboardID",
 *          description="DesDashboardID",
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
class HrmsDesignation extends Model
{

    public $table = 'srp_designation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $appends = ['designation'];

    public $fillable = [
        'DesDescription',
        'isRequiredSelection',
        'SelectionID',
        'DesDashboardID',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'isDeleted',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
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
        'DesignationID' => 'integer',
        'DesDescription' => 'string',
        'isRequiredSelection' => 'integer',
        'SelectionID' => 'integer',
        'DesDashboardID' => 'integer',
        'SchMasterID' => 'integer',
        'BranchID' => 'integer',
        'Erp_companyID' => 'integer',
        'isDeleted' => 'integer',
        'CreatedUserName' => 'string',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
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

    public function getDesignationAttribute()
    {
        return $this->DesDescription;
    }
}
