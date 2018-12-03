<?php
/**
 * =============================================
 * -- File Name : SupplierCategoryICVSub.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Category ICV Sub
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierCategoryICVSub",
 *      required={""},
 *      @SWG\Property(
 *          property="supCategorySubICVID",
 *          description="supCategorySubICVID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supCategoryICVMasterID",
 *          description="supCategoryICVMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subCategoryCode",
 *          description="subCategoryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="categoryDescription",
 *          description="categoryDescription",
 *          type="string"
 *      )
 * )
 */
class SupplierCategoryICVSub extends Model
{

    public $table = 'suppliercategoryicvsub';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'supCategorySubICVID';


    public $fillable = [
        'supCategoryICVMasterID',
        'subCategoryCode',
        'categoryDescription',
        'timeStamp',
        'createdDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supCategorySubICVID' => 'integer',
        'supCategoryICVMasterID' => 'integer',
        'subCategoryCode' => 'string',
        'categoryDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
