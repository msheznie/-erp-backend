<?php
/**
 * =============================================
 * -- File Name : SupplierCatalogMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Catalog
 * -- Author : Mohamed Rilwan
 * -- Create date : 01 - April 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierCatalogMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="supplierCatalogMasterID",
 *          description="supplierCatalogMasterID",
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
 *          property="supplierID",
 *          description="supplierID",
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
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="boolean"
 *      )
 * )
 */
class SupplierCatalogMaster extends Model
{

    public $table = 'erp_supplier_catalog_master';
    
    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';

    protected $primaryKey  = 'supplierCatalogMasterID';

    public $fillable = [
        'catalogID',
        'catalogName',
        'fromDate',
        'toDate',
        'supplierID',
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
        'supplierCatalogMasterID' => 'integer',
        'catalogID' => 'string',
        'catalogName' => 'string',
        'fromDate' => 'datetime',
        'toDate' => 'datetime',
        'supplierID' => 'integer',
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
//        'supplierCatalogMasterID' => 'required'
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
        return $this->hasMany('App\Models\SupplierCatalogDetail', 'supplierCatalogMasterID', 'supplierCatalogMasterID');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function data()
    {
        return $this->belongsTo('App\Models\SupplierCatalogDetail', 'supplierCatalogMasterID', 'supplierCatalogMasterID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

}
