<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerMasterCategoryAssigned",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerMasterCategoryID",
 *          description="customerMasterCategoryID",
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
 *          property="categoryDescription",
 *          description="categoryDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isAssigned",
 *          description="isAssigned",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="boolean"
 *      )
 * )
 */
class CustomerMasterCategoryAssigned extends Model
{

    public $table = 'customermastercategory_assigned';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = null;




    public $fillable = [
        'customerMasterCategoryID',
        'companySystemID',
        'categoryDescription',
        'createdUserID',
        'createdDateTime',
        'isAssigned',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customerMasterCategoryID' => 'integer',
        'companySystemID' => 'integer',
        'categoryDescription' => 'string',
        'createdUserID' => 'integer',
        'createdDateTime' => 'datetime',
        'isAssigned' => 'boolean',
        'isActive' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

     public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public static function checkCustomerCategoryAssignedStatus($customerMasterCategoryID, $companySystemID)
    {
         return CustomerMasterCategoryAssigned::where('customerMasterCategoryID', $customerMasterCategoryID)
                                              ->where('companySystemID', $companySystemID)       
                                              ->where('isAssigned', 1)       
                                              ->where('isActive', 1)
                                              ->first();       
    }
}
