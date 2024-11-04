<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CurrencyConversionMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="conversionCode",
 *          description="conversionCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="conversionDate",
 *          description="conversionDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpName",
 *          description="confirmedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ConfirmedBy",
 *          description="ConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ConfirmedBySystemID",
 *          description="ConfirmedBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedby",
 *          description="approvedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedEmpSystemID",
 *          description="approvedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CurrencyConversionMaster extends Model
{

    public $table = 'currency_conversion_master';
    
    const CREATED_AT = 'conversionDate';
    const UPDATED_AT = 'timeStamp';




    public $fillable = [
        'conversionCode',
        'conversionDate',
        'createdBy',
        'description',
        'confirmedYN',
        'confirmedEmpName',
        'ConfirmedBy',
        'ConfirmedBySystemID',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'approvedEmpSystemID',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'serialNumber',
        'timeStamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'conversionCode' => 'string',
        'conversionDate' => 'datetime',
        'createdBy' => 'integer',
        'description' => 'string',
        'confirmedYN' => 'integer',
        'serialNumber' => 'integer',
        'confirmedEmpName' => 'string',
        'ConfirmedBy' => 'string',
        'ConfirmedBySystemID' => 'integer',
        'confirmedDate' => 'datetime',
        'approvedDate' => 'datetime',
        'approvedYN' => 'integer',
        'approvedby' => 'string',
        'approvedEmpSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'timeStamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
    public function created_by(){
        return $this->belongsTo('App\Models\Employee','createdBy','employeeSystemID');
    }

     public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'ConfirmedBySystemID', 'employeeSystemID');
    }
}
