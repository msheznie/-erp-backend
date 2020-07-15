<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="QuotationStatusMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="quotationStatusMasterID",
 *          description="quotationStatusMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quotationStatus",
 *          description="quotationStatus",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isAdmin",
 *          description="isAdmin",
 *          type="boolean"
 *      )
 * )
 */
class QuotationStatusMaster extends Model
{

    public $table = 'quotationstatusmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'quotationStatus',
        'isAdmin'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'quotationStatusMasterID' => 'integer',
        'quotationStatus' => 'string',
        'isAdmin' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
