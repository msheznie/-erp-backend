<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AccruavalFromOPMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="accruvalMasterID",
 *          description="accruvalMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accruvalNarration",
 *          description="accruvalNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accmonth",
 *          description="accmonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accYear",
 *          description="accYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accConfirmedYN",
 *          description="accConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accConfirmedBy",
 *          description="accConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jvMasterAutoID",
 *          description="jvMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accJVpostedYN",
 *          description="accJVpostedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jvPostedBy",
 *          description="jvPostedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdby",
 *          description="createdby",
 *          type="string"
 *      )
 * )
 */
class AccruavalFromOPMaster extends Model
{

    public $table = 'accruavalfromopmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'accruvalMasterID';

    public $fillable = [
        'accruvalNarration',
        'accrualDateAsOF',
        'serialNo',
        'companyID',
        'accmonth',
        'accYear',
        'accConfirmedYN',
        'accConfirmedBy',
        'accConfirmedDate',
        'jvMasterAutoID',
        'accJVpostedYN',
        'jvPostedBy',
        'jvPostedDate',
        'createdby',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'accruvalMasterID' => 'integer',
        'accruvalNarration' => 'string',
        'serialNo' => 'integer',
        'companyID' => 'string',
        'accmonth' => 'integer',
        'accYear' => 'integer',
        'accConfirmedYN' => 'integer',
        'accConfirmedBy' => 'string',
        'jvMasterAutoID' => 'integer',
        'accJVpostedYN' => 'integer',
        'jvPostedBy' => 'string',
        'createdby' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
