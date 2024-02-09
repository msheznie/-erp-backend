<?php

namespace App\Models;

use App\Traits\ActiveTrait;
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

    use ActiveTrait;

    public $table = 'erp_fa_financecategory';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'faFinanceCatID';

    public $fillable = [
        'financeCatDescription',
        'COSTGLCODESystemID',
        'COSTGLCODE',
        'ACCDEPGLCODESystemID',
        'ACCDEPGLCODE',
        'DEPGLCODESystemID',
        'DEPGLCODE',
        'DISPOGLCODESystemID',
        'DISPOGLCODE',
        'isActive',
        'sortOrder',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'lifeTimeInYears',
        'createdDateTime',
        'formula',
        'currentSerialNumber',
        'modifiedPc',
        'modifiedUser',
        'timestamp',
        'serializationBasedOn',
        'enableEditing' 
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'faFinanceCatID' => 'integer',
        'financeCatDescription' => 'string',
        'COSTGLCODESystemID' => 'integer',
        'COSTGLCODE' => 'string',
        'ACCDEPGLCODESystemID' => 'integer',
        'ACCDEPGLCODE' => 'string',
        'DEPGLCODESystemID' => 'integer',
        'DEPGLCODE' => 'string',
        'lifeTimeInYears' => 'float',
        'DISPOGLCODESystemID' => 'integer',
        'DISPOGLCODE' => 'string',
        'isActive' => 'integer',
        'sortOrder' => 'integer',
        'currentSerialNumber' => 'integer',
        'createdPcID' => 'string',
        'createdUserGroup' => 'string',
        'formula' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'serializationBasedOn' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function costaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'COSTGLCODESystemID', 'chartOfAccountSystemID');
    }

    public function accdepaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'ACCDEPGLCODESystemID', 'chartOfAccountSystemID');
    }

    public function depaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'DEPGLCODESystemID', 'chartOfAccountSystemID');
    }

    public function disaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'DISPOGLCODESystemID','chartOfAccountSystemID');
    }
    
}
