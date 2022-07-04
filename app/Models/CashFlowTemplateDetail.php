<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CashFlowTemplateDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cashFlowTemplateID",
 *          description="cashFlowTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterID",
 *          description="masterID",
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
 *          property="subExits",
 *          description="subExits",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logicType",
 *          description="logicType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="controlAccountType",
 *          description="controlAccountType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CashFlowTemplateDetail extends Model
{

    public $table = 'cash_flow_template_details';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'cashFlowTemplateID',
        'description',
        'type',
        'masterID',
        'sortOrder',
        'subExits',
        'logicType',
        'controlAccountType',
        'createdPCID',
        'createdUserSystemID',
        'manualGlMapping',
        'proceedPaymentSelection',
        'proceedPaymentType',
        'isDefault',
        'isFinalLevel',
        'modifiedPCID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cashFlowTemplateID' => 'integer',
        'description' => 'string',
        'type' => 'integer',
        'masterID' => 'integer',
        'sortOrder' => 'integer',
        'subExits' => 'integer',
        'logicType' => 'integer',
        'controlAccountType' => 'integer',
        'isFinalLevel' => 'integer',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'manualGlMapping' => 'integer',
        'proceedPaymentType' => 'integer',
        'proceedPaymentSelection' => 'integer',
        'isDefault' => 'integer',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function scopeOfMaster($query, $cashFlowTemplateID)
    {
        return $query->where('cashFlowTemplateID',  $cashFlowTemplateID);
    }

    public function subcategory()
    {
        return $this->hasMany(CashFlowTemplateDetail::class,'masterID','id');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\CashFlowTemplate','cashFlowTemplateID','id');
    }

    public function gllink()
    {
        return $this->hasMany('App\Models\CashFlowTemplateLink','templateDetailID','id');
    }

    public function subcatlink()
    {
        return $this->hasMany('App\Models\CashFlowTemplateLink','subCategory','id');
    }

    public function subcategorytot()
    {
        return $this->hasMany('App\Models\CashFlowTemplateLink','templateDetailID','id');
    }

    public function gl_codes()
    {
        return $this->gllink();
    }
}
