<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="InterCompanyAssetDisposal",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetDisposalID",
 *          description="assetDisposalID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerInvoiceID",
 *          description="customerInvoiceID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvID",
 *          description="grvID",
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
class InterCompanyAssetDisposal extends Model
{

    public $table = 'inter_company_asset_disposal';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'assetDisposalID',
        'customerInvoiceID',
        'grvID',
        'supplierInvoiceID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'assetDisposalID' => 'integer',
        'customerInvoiceID' => 'integer',
        'grvID' => 'integer',
        'supplierInvoiceID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
