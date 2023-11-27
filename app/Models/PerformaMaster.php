<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PerformaMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="PerformaMasterID",
 *          description="PerformaMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PerformaInvoiceNo",
 *          description="PerformaInvoiceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaSerialNO",
 *          description="performaSerialNO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PerformaCode",
 *          description="PerformaCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientID",
 *          description="clientID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaDate",
 *          description="performaDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaStatus",
 *          description="performaStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PerformaOpConfirmed",
 *          description="PerformaOpConfirmed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaOpConfirmedBy",
 *          description="performaOpConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PerformaFinanceConfirmed",
 *          description="PerformaFinanceConfirmed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaFinanceConfirmedBy",
 *          description="performaFinanceConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaValue",
 *          description="performaValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="ticketNo",
 *          description="ticketNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankID",
 *          description="bankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountID",
 *          description="accountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentInDaysForJob",
 *          description="paymentInDaysForJob",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custInvNoModified",
 *          description="custInvNoModified",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPerformaOnEditRental",
 *          description="isPerformaOnEditRental",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRefBackBillingYN",
 *          description="isRefBackBillingYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refBackBillingBy",
 *          description="refBackBillingBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isRefBackOPYN",
 *          description="isRefBackOPYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refBackOPby",
 *          description="refBackOPby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refBillingComment",
 *          description="refBillingComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refOpComment",
 *          description="refOpComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientAppPerformaType",
 *          description="clientAppPerformaType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientapprovedBy",
 *          description="clientapprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaSentToHO",
 *          description="performaSentToHO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaSentToHOEmpID",
 *          description="performaSentToHOEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lotSystemAutoID",
 *          description="lotSystemAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lotNumber",
 *          description="lotNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaReceivedByEmpID",
 *          description="performaReceivedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="submittedToClientByEmpID",
 *          description="submittedToClientByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isAccrualYN",
 *          description="isAccrualYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCanceledYN",
 *          description="isCanceledYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceCompanyID",
 *          description="serviceCompanyID",
 *          type="string"
 *      )
 * )
 */
class PerformaMaster extends Model
{

    public $table = 'performamaster';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';


    public $fillable = [
        'PerformaInvoiceNo',
        'performaSerialNO',
        'PerformaCode',
        'companyID',
        'companySystemID',
        'customerSystemID',
        'serviceLine',
        'clientID',
        'contractID',
        'performaDate',
        'createdUserID',
        'modifiedUserID',
        'performaStatus',
        'PerformaOpConfirmed',
        'performaOpConfirmedBy',
        'performaOpConfirmedDate',
        'PerformaFinanceConfirmed',
        'performaFinanceConfirmedBy',
        'performaFinanceConfirmedDate',
        'performaValue',
        'ticketNo',
        'bankID',
        'accountID',
        'paymentInDaysForJob',
        'custInvNoModified',
        'isPerformaOnEditRental',
        'isRefBackBillingYN',
        'refBackBillingBy',
        'refBackBillingDate',
        'isRefBackOPYN',
        'refBackOPby',
        'refBackOpDate',
        'refBillingComment',
        'refOpComment',
        'clientAppPerformaType',
        'clientapprovedDate',
        'clientapprovedBy',
        'performaSentToHO',
        'performaSentToHODate',
        'performaSentToHOEmpID',
        'lotSystemAutoID',
        'lotNumber',
        'performaReceivedByEmpID',
        'performaReceivedByDate',
        'submittedToClientDate',
        'submittedToClientByEmpID',
        'receivedFromClientDate',
        'reSubmittedDate',
        'approvedByClientDate',
        'timeStamp',
        'isAccrualYN',
        'isCanceledYN',
        'serviceCompanyID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'PerformaMasterID' => 'integer',
        'PerformaInvoiceNo' => 'integer',
        'performaSerialNO' => 'integer',
        'PerformaCode' => 'string',
        'companyID' => 'string',
        'serviceLine' => 'string',
        'clientID' => 'string',
        'contractID' => 'string',
        'performaDate' => 'date',
        'createdUserID' => 'string',
        'modifiedUserID' => 'string',
        'performaStatus' => 'integer',
        'PerformaOpConfirmed' => 'integer',
        'performaOpConfirmedBy' => 'string',
        'PerformaFinanceConfirmed' => 'integer',
        'performaFinanceConfirmedBy' => 'string',
        'performaValue' => 'float',
        'ticketNo' => 'integer',
        'bankID' => 'integer',
        'accountID' => 'integer',
        'paymentInDaysForJob' => 'integer',
        'custInvNoModified' => 'integer',
        'isPerformaOnEditRental' => 'integer',
        'isRefBackBillingYN' => 'integer',
        'refBackBillingBy' => 'string',
        'isRefBackOPYN' => 'integer',
        'refBackOPby' => 'string',
        'refBillingComment' => 'string',
        'refOpComment' => 'string',
        'clientAppPerformaType' => 'integer',
        'clientapprovedBy' => 'string',
        'performaSentToHO' => 'integer',
        'performaSentToHOEmpID' => 'string',
        'lotSystemAutoID' => 'integer',
        'lotNumber' => 'string',
        'performaReceivedByEmpID' => 'string',
        'submittedToClientByEmpID' => 'string',
        'isAccrualYN' => 'integer',
        'isCanceledYN' => 'integer',
        'serviceCompanyID' => 'string',
        'companySystemID' => 'integer',
        'customerSystemID' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


    public function performaTemp()
    {
        return $this->hasMany('App\Models\PerformaTemp', 'performaMasterID', 'PerformaMasterID');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\TicketMaster', 'ticketNo', 'ticketidAtuto');
    }


}
