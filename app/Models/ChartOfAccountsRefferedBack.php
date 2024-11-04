<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountsRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name : Chart Of Accounts Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ChartOfAccountsRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="chartOfAccountSystemIDRefferedBack",
 *          description="chartOfAccountSystemIDRefferedBack",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanySystemID",
 *          description="primaryCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanyID",
 *          description="primaryCompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
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
 *          property="masterAccount",
 *          description="masterAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="catogaryBLorPLID",
 *          description="catogaryBLorPLID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="catogaryBLorPL",
 *          description="catogaryBLorPL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="controllAccountYN",
 *          description="controllAccountYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="controlAccountsSystemID",
 *          description="controlAccountsSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="controlAccounts",
 *          description="controlAccounts",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isApproved",
 *          description="isApproved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedBySystemID",
 *          description="approvedBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedBy",
 *          description="approvedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedComment",
 *          description="approvedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isBank",
 *          description="isBank",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="AllocationID",
 *          description="AllocationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="relatedPartyYN",
 *          description="relatedPartyYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="interCompanySystemID",
 *          description="interCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="interCompanyID",
 *          description="interCompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpSystemID",
 *          description="confirmedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpID",
 *          description="confirmedEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpName",
 *          description="confirmedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isMasterAccount",
 *          description="isMasterAccount",
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
class ChartOfAccountsRefferedBack extends Model
{

    public $table = 'chartofaccounts_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'chartOfAccountSystemIDRefferedBack';



    public $fillable = [
        'chartOfAccountSystemID',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPLID',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccountsSystemID',
        'controlAccounts',
        'isApproved',
        'approvedBySystemID',
        'approvedBy',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'interCompanySystemID',
        'interCompanyID',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedEmpDate',
        'isMasterAccount',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp',
          'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'chartOfAccountSystemIDRefferedBack' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'AccountCode' => 'string',
        'AccountDescription' => 'string',
        'masterAccount' => 'string',
        'catogaryBLorPLID' => 'integer',
        'catogaryBLorPL' => 'string',
        'controllAccountYN' => 'integer',
        'controlAccountsSystemID' => 'integer',
        'controlAccounts' => 'string',
        'isApproved' => 'integer',
        'approvedBySystemID' => 'integer',
        'approvedBy' => 'string',
        'approvedComment' => 'string',
        'isActive' => 'integer',
        'isBank' => 'integer',
        'AllocationID' => 'integer',
        'relatedPartyYN' => 'integer',
        'interCompanySystemID' => 'integer',
        'interCompanyID' => 'string',
        'confirmedYN' => 'integer',
        'confirmedEmpSystemID' => 'integer',
        'confirmedEmpID' => 'string',
        'confirmedEmpName' => 'string',
        'isMasterAccount' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
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

    public function controlAccount()
    {
        /** one control account  can have many chart of accounts */
        return $this->belongsTo('App\Models\ControlAccount', 'controlAccountsSystemID', 'controlAccountsSystemID');
    }

    public function accountType()
    {
        /** one Account Type can related to many chart of accounts */
        return $this->belongsTo('App\Models\AccountsType', 'catogaryBLorPLID', 'accountsType');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','approvedBySystemID','employeeSystemID');
    }

}
