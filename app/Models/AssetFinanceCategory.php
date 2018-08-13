<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetFinanceCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="faFinanceCatID",
 *          description="faFinanceCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeCatDescription",
 *          description="financeCatDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="COSTGLCODE",
 *          description="COSTGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ACCDEPGLCODE",
 *          description="ACCDEPGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DEPGLCODE",
 *          description="DEPGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DISPOGLCODE",
 *          description="DISPOGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
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
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
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
 *      )
 * )
 */
class AssetFinanceCategory extends Model
{

    public $table = 'erp_fa_financecategory';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'financeCatDescription',
        'COSTGLCODE',
        'ACCDEPGLCODE',
        'DEPGLCODE',
        'DISPOGLCODE',
        'isActive',
        'sortOrder',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'faFinanceCatID' => 'integer',
        'financeCatDescription' => 'string',
        'COSTGLCODE' => 'string',
        'ACCDEPGLCODE' => 'string',
        'DEPGLCODE' => 'string',
        'DISPOGLCODE' => 'string',
        'isActive' => 'integer',
        'sortOrder' => 'integer',
        'createdPcID' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
