<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="QuotationStatus",
 *      required={""},
 *      @SWG\Property(
 *          property="quotationStatusID",
 *          description="quotationStatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quotationID",
 *          description="quotationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quotationStatusMasterID",
 *          description="quotationStatusMasterID",
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
 *          property="quotationStatusDate",
 *          description="quotationStatusDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class QuotationStatus extends Model
{

    public $table = 'quotationstatus';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'quotationStatusID';


    public $fillable = [
        'quotationID',
        'quotationStatusMasterID',
        'companySystemID',
        'quotationStatusDate',
        'comments',
        'createdDateTime',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'quotationStatusID' => 'integer',
        'quotationID' => 'integer',
        'quotationStatusMasterID' => 'integer',
        'companySystemID' => 'integer',
        'quotationStatusDate' => 'datetime',
        'comments' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\QuotationStatusMaster', 'quotationStatusMasterID', 'quotationStatusMasterID');
    }

    public static function getLastStatus($quotationID)
    {
        $quotationStatusData = QuotationStatus::where('quotationID', $quotationID)
                                              ->with(['status'])
                                              ->orderBy('quotationStatusID', 'desc')
                                              ->first();

        $status = '';
        if (!empty($quotationStatusData)) {
            $status = $quotationStatusData->status->quotationStatus;
        }

        return $status;
    }
}
