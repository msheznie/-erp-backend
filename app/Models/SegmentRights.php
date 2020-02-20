<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SegmentRights",
 *      required={""},
 *      @SWG\Property(
 *          property="companyrightsID",
 *          description="companyrightsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPcID",
 *          description="modifiedPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="segmentRightsID",
 *          description="segmentRightsID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SegmentRights extends Model
{

    public $table = 'segmentrights';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'companyrightsID',
        'employeeSystemID',
        'companySystemID',
        'serviceLineSystemID',
        'createdUserSystemID',
        'createdPcID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedPcID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyrightsID' => 'integer',
        'employeeSystemID' => 'integer',
        'companySystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdPcID' => 'string',
        'createdDateTime' => 'datetime',
        'modifiedUserSystemID' => 'integer',
        'modifiedPcID' => 'string',
        'modifiedDateTime' => 'datetime',
        'timestamp' => 'datetime',
        'segmentRightsID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'segmentRightsID' => 'required'
    ];

    
}
