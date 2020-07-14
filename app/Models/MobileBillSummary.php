<?php
/**
 * =============================================
 * -- File Name : MobileBillSummary.php
 * -- Project Name : ERP
 * -- Module Name : MobileBillMaster
 * -- Author : Mohamed Rilwan
 * -- Create date : 12- July 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MobileBillSummary",
 *      required={""},
 *      @SWG\Property(
 *          property="mobileBillSummaryID",
 *          description="mobileBillSummaryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobileMasterID",
 *          description="mobileMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobileNumber",
 *          description="mobileNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rental",
 *          description="rental",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="setUpFee",
 *          description="setUpFee",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localCharges",
 *          description="localCharges",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="internationalCallCharges",
 *          description="internationalCallCharges",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="domesticSMS",
 *          description="domesticSMS",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="internationalSMS",
 *          description="internationalSMS",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="domesticMMS",
 *          description="domesticMMS",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="internationalMMS",
 *          description="internationalMMS",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discounts",
 *          description="discounts",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="otherCharges",
 *          description="otherCharges",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="blackberryCharges",
 *          description="blackberryCharges",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="roamingCharges",
 *          description="roamingCharges",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="GPRSPayG",
 *          description="GPRSPayG",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="GPRSPKG",
 *          description="GPRSPKG",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalCurrentCharges",
 *          description="totalCurrentCharges",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="billDate",
 *          description="billDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class MobileBillSummary extends Model
{

    public $table = 'hrms_mobilebillsummary';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'mobileBillSummaryID';



    public $fillable = [
        'mobileMasterID',
        'mobileNumber',
        'rental',
        'setUpFee',
        'localCharges',
        'internationalCallCharges',
        'domesticSMS',
        'internationalSMS',
        'domesticMMS',
        'internationalMMS',
        'discounts',
        'otherCharges',
        'blackberryCharges',
        'roamingCharges',
        'GPRSPayG',
        'GPRSPKG',
        'totalCurrentCharges',
        'billDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobileBillSummaryID' => 'integer',
        'mobileMasterID' => 'integer',
        'mobileNumber' => 'integer',
        'rental' => 'float',
        'setUpFee' => 'float',
        'localCharges' => 'float',
        'internationalCallCharges' => 'float',
        'domesticSMS' => 'float',
        'internationalSMS' => 'float',
        'domesticMMS' => 'float',
        'internationalMMS' => 'float',
        'discounts' => 'float',
        'otherCharges' => 'float',
        'blackberryCharges' => 'float',
        'roamingCharges' => 'float',
        'GPRSPayG' => 'float',
        'GPRSPKG' => 'float',
        'totalCurrentCharges' => 'float',
        'billDate' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'mobileMasterID' => 'required',
//        'mobileNumber' => 'required'
    ];

    public function mobile_pool(){
        return $this->belongsTo('App\Models\MobileNoPool', 'mobileNumber','mobileNo');
    }
    
}
