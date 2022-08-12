<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PosSourceMenuCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="menuCategoryID",
 *          description="menuCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuCategoryDescription",
 *          description="Eg: Sandwithches, Beverages etc...",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="Thumbnail Image",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLAutoID",
 *          description="revenueGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="topSalesRptYN",
 *          description="topSalesRptYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPack",
 *          description="0 - not pack, 1 pack (bunlde)",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterLevelID",
 *          description="if null no level exist, this is to maintain the level ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="levelNo",
 *          description="master level 0 ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bgColor",
 *          description="background color for category",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showImageYN",
 *          description="showImageYN",
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
 *          property="deletedBy",
 *          description="deletedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deletedDatetime",
 *          description="deletedDatetime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PosSourceMenuCategory extends Model
{

    public $table = 'pos_source_menucategory';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'menuCategoryDescription',
        'image',
        'revenueGLAutoID',
        'topSalesRptYN',
        'companyID',
        'sortOrder',
        'isPack',
        'masterLevelID',
        'levelNo',
        'bgColor',
        'isActive',
        'showImageYN',
        'isDeleted',
        'deletedBy',
        'deletedDatetime',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'createdUserGroup',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timeStamp',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menuCategoryID' => 'integer',
        'menuCategoryDescription' => 'string',
        'image' => 'string',
        'revenueGLAutoID' => 'integer',
        'topSalesRptYN' => 'integer',
        'companyID' => 'integer',
        'sortOrder' => 'integer',
        'isPack' => 'integer',
        'masterLevelID' => 'integer',
        'levelNo' => 'integer',
        'bgColor' => 'string',
        'isActive' => 'integer',
        'showImageYN' => 'integer',
        'isDeleted' => 'integer',
        'deletedBy' => 'string',
        'deletedDatetime' => 'datetime',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'createdUserGroup' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timeStamp' => 'datetime',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'menuCategoryDescription' => 'required'
    ];

    
}
