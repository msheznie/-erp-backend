<?php
/**
 * =============================================
 * -- File Name : LogisticShippingStatus.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LogisticShippingStatus",
 *      required={""},
 *      @SWG\Property(
 *          property="logisticShippingStatusID",
 *          description="logisticShippingStatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticMasterID",
 *          description="logisticMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shippingStatusID",
 *          description="shippingStatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="statusComment",
 *          description="statusComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      )
 * )
 */
class LogisticShippingStatus extends Model
{

    public $table = 'erp_logisticshippingstatus';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'logisticShippingStatusID';


    public $fillable = [
        'logisticMasterID',
        'shippingStatusID',
        'statusDate',
        'statusComment',
        'createdUserID',
        'createdPCID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'logisticShippingStatusID' => 'integer',
        'logisticMasterID' => 'integer',
        'shippingStatusID' => 'integer',
        'statusComment' => 'string',
        'createdUserID' => 'string',
        'createdPCID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function status(){
        return $this->belongsTo('App\Models\LogisticStatus','shippingStatusID','StatusID');
    }

}
