<?php
/**
 * =============================================
 * -- File Name : HRMSChartOfAccounts.php
 * -- Project Name : ERP
 * -- Module Name :  HRMS Chart Of Accounts
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSChartOfAccounts",
 *      required={""},
 *      @SWG\Property(
 *          property="charofAccAutoID",
 *          description="charofAccAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="AccountCode",
 *          description="AccountCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="AccountDescription",
 *          description="AccountDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empGroup",
 *          description="empGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class HRMSChartOfAccounts extends Model
{

    public $table = 'hrms_chartofaccounts';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey  = 'charofAccAutoID';


    public $fillable = [
        'AccountCode',
        'AccountDescription',
        'empGroup',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'charofAccAutoID' => 'integer',
        'AccountCode' => 'string',
        'AccountDescription' => 'string',
        'empGroup' => 'integer',
        'createdPcID' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
