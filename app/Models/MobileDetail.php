<?php
/**
 * =============================================
 * -- File Name : MobileDetail.php
 * -- Project Name : ERP
 * -- Module Name : Mobile Bill Management
 * -- Author : Mohamed Rilwan
 * -- Create date : 12- July 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MobileDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="mobileDetailID",
 *          description="mobileDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobilebillMasterID",
 *          description="mobilebillMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="billPeriod",
 *          description="billPeriod",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startDate",
 *          description="startDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="EndDate",
 *          description="EndDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="myNumber",
 *          description="myNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DestCountry",
 *          description="DestCountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DestNumber",
 *          description="DestNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="duration",
 *          description="duration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="callDate",
 *          description="callDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="cost",
 *          description="cost",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Narration",
 *          description="Narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyID",
 *          description="rptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyER",
 *          description="rptCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isOfficial",
 *          description="isOfficial",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isIDD",
 *          description="isIDD",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="1- International Calls, 2- Roaming Calls, 3 -Calls Received while Roaming, 4 - International SMS",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="userComments",
 *          description="userComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createUserID",
 *          description="createUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createPCID",
 *          description="createPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class MobileDetail extends Model
{

    public $table = 'hrms_mobiledetail';
    
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'mobileDetailID';


    public $fillable = [
        'mobilebillMasterID',
        'billPeriod',
        'startDate',
        'EndDate',
        'myNumber',
        'DestCountry',
        'DestNumber',
        'duration',
        'callDate',
        'cost',
        'currency',
        'Narration',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'isOfficial',
        'isIDD',
        'type',
        'userComments',
        'createDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobileDetailID' => 'integer',
        'mobilebillMasterID' => 'integer',
        'billPeriod' => 'integer',
        'startDate' => 'datetime',
        'EndDate' => 'datetime',
        'myNumber' => 'integer',
        'DestCountry' => 'string',
        'DestNumber' => 'string',
        'duration' => 'string',
        'callDate' => 'datetime',
        'cost' => 'float',
        'currency' => 'integer',
        'Narration' => 'string',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'rptCurrencyID' => 'integer',
        'rptCurrencyER' => 'float',
        'rptAmount' => 'float',
        'isOfficial' => 'integer',
        'isIDD' => 'integer',
        'type' => 'integer',
        'userComments' => 'string',
        'createDate' => 'datetime',
        'createUserID' => 'string',
        'createPCID' => 'string',
        'modifiedpc' => 'string',
        'modifiedUser' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function mobile_pool(){
        return $this->belongsTo('App\Models\MobileNoPool', 'myNumber','mobileNo');
    }
    
}
