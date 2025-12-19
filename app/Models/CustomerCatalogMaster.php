<?php
/**
 * =============================================
 * -- File Name : SupplierCatalogMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Catalog
 * -- Author : Mohamed Rilwan
 * -- Create date : 08 - April 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerCatalogMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="customerCatalogMasterID",
 *          description="customerCatalogMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="catalogID",
 *          description="catalogID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="catalogName",
 *          description="catalogName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fromDate",
 *          description="fromDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="toDate",
 *          description="toDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
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
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDate",
 *          description="createdDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedBy",
 *          description="modifiedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDate",
 *          description="modifiedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isDelete",
 *          description="isDelete",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CustomerCatalogMaster extends Model
{

    public $table = 'erp_customer_catalog_master';

    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';

    protected $primaryKey  = 'customerCatalogMasterID';

    public $fillable = [
        'catalogID',
        'catalogName',
        'fromDate',
        'toDate',
        'customerID',
        'companySystemID',
        'documentSystemID',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'isDeleted',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerCatalogMasterID' => 'integer',
        'catalogID' => 'string',
        'catalogName' => 'string',
        'fromDate' => 'datetime',
        'toDate' => 'datetime',
        'customerID' => 'integer',
        'companySystemID' => 'integer',
        'documentSystemID' => 'integer',
        'createdBy' => 'integer',
        'createdDate' => 'datetime',
        'modifiedBy' => 'string',
        'modifiedDate' => 'datetime',
        'isDeleted' => 'integer',
        'isActive' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'customerCatalogMasterID' => 'required',
//        'isDelete' => 'required',
//        'isActive' => 'required'
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdBy', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedBy', 'employeeSystemID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\CustomerCatalogDetail', 'customerCatalogMasterID', 'customerCatalogMasterID');
    }

    
}
