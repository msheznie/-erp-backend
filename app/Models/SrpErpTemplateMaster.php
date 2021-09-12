<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SrpErpTemplateMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="TempMasterID",
 *          description="TempMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="TempDes",
 *          description="TempDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="TempPageName",
 *          description="TempPageName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="TempPageNameLink",
 *          description="TempPageNameLink",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createPageLink",
 *          description="createPageLink",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="FormCatID",
 *          description="FormCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isReport",
 *          description="isReport",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="Identify the view template",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateKey",
 *          description="templateKey",
 *          type="string"
 *      )
 * )
 */
class SrpErpTemplateMaster extends Model
{

    public $table = 'srp_erp_templatemaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'TempDes',
        'TempPageName',
        'TempPageNameLink',
        'createPageLink',
        'FormCatID',
        'isReport',
        'isDefault',
        'documentCode',
        'templateKey'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'TempMasterID' => 'integer',
        'TempDes' => 'string',
        'TempPageName' => 'string',
        'TempPageNameLink' => 'string',
        'createPageLink' => 'string',
        'FormCatID' => 'integer',
        'isReport' => 'integer',
        'isDefault' => 'integer',
        'documentCode' => 'string',
        'templateKey' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    
}
