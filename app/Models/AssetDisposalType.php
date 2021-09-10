<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetDisposalType",
 *      required={""},
 *      @SWG\Property(
 *          property="disposalTypesID",
 *          description="disposalTypesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="typeDescription",
 *          description="typeDescription",
 *          type="string"
 *      )
 * )
 */
class AssetDisposalType extends Model
{

    public $table = 'erp_fa_asset_disposaltypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'disposalTypesID';

    public $fillable = [
        'typeDescription',
        'activeYN',
        'chartOfAccountID',
        'updated_by',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'disposalTypesID' => 'integer',
        'activeYN' => 'integer',
        'chartOfAccountID' => 'integer',
        'typeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountID', 'chartOfAccountSystemID');
    }

    
}
