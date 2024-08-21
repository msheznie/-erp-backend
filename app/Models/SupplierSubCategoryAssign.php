<?php
/**
 * =============================================
 * -- File Name : SupplierSubCategoryAssign.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Sub Category Assign
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierSubCategoryAssign
 * @package App\Models
 * @version February 28, 2018, 8:49 am UTC
 *
 * @property integer supplierID
 * @property integer supSubCategoryID
 * @property string|\Carbon\Carbon timestamp
 */
class SupplierSubCategoryAssign extends Model
{
    //use SoftDeletes;

    public $table = 'suppliersubcategoryassign';
    protected $primaryKey = 'supplierSubCategoryAssignID';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = null; //'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'supplierID',
        'supSubCategoryID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierSubCategoryAssignID' => 'integer',
        'supplierID' => 'integer',
        'supSubCategoryID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function categoryMaster(){
        return $this->hasOne('App\Models\suppliercategorysub', 'supCategorySubID','supSubCategoryID');
    }
    
}
