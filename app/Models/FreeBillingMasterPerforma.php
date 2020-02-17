<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FreeBillingMasterPerforma",
 *      required={""},
 *      @SWG\Property(
 *          property="idbillingMasterPerforma",
 *          description="idbillingMasterPerforma",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BillProcessNO",
 *          description="BillProcessNO",
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
 *          property="PerformaInvoiceText",
 *          description="PerformaInvoiceText",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ticketno",
 *          description="Ticketno",
 *          type="integer",
 *          format="int32"
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
 *          property="performaStatus",
 *          description="performaStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BillProcessDate",
 *          description="BillProcessDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="SelectedForPerformaYN",
 *          description="SelectedForPerformaYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="InvoiceNo",
 *          description="InvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PerformaOpConfirmed",
 *          description="PerformaOpConfirmed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PerformaFinanceConfirmed",
 *          description="PerformaFinanceConfirmed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaOpConfirmedBy",
 *          description="performaOpConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaFinanceConfirmedBy",
 *          description="performaFinanceConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedBy",
 *          description="confirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedBy",
 *          description="approvedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="billingCode",
 *          description="billingCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaSerialNo",
 *          description="performaSerialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaCode",
 *          description="performaCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rentalType",
 *          description="rentalType",
 *          type="integer",
 *          format="int32"
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
 *          property="performaMasterID",
 *          description="performaMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isTrasportRental",
 *          description="isTrasportRental",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="disableRental",
 *          description="disableRental",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="IsOpStbDaysFromMIT",
 *          description="IsOpStbDaysFromMIT",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class FreeBillingMasterPerforma extends Model
{

    public $table = 'freebillingmasterperforma';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'idbillingMasterPerforma';

    public $fillable = [
        'BillProcessNO',
        'PerformaInvoiceNo',
        'PerformaInvoiceText',
        'Ticketno',
        'clientID',
        'clientSystemID',
        'contractID',
        'contractUID',
        'performaDate',
        'performaStatus',
        'BillProcessDate',
        'SelectedForPerformaYN',
        'InvoiceNo',
        'PerformaOpConfirmed',
        'PerformaFinanceConfirmed',
        'performaOpConfirmedBy',
        'performaOpConfirmedDate',
        'performaFinanceConfirmedBy',
        'performaFinanceConfirmedDate',
        'confirmedYN',
        'confirmedBy',
        'confirmedDate',
        'confirmedByName',
        'approvedYN',
        'approvedBy',
        'approvedDate',
        'documentID',
        'documentSystemID',
        'companyID',
        'companySystemID',
        'serviceLineCode',
        'serviceLineSystemID',
        'serialNo',
        'billingCode',
        'performaSerialNo',
        'performaCode',
        'rentalStartDate',
        'rentalEndDate',
        'rentalType',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserID',
        'timeStamp',
        'performaMasterID',
        'isTrasportRental',
        'disableRental',
        'IsOpStbDaysFromMIT'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idbillingMasterPerforma' => 'integer',
        'BillProcessNO' => 'integer',
        'PerformaInvoiceNo' => 'integer',
        'PerformaInvoiceText' => 'string',
        'Ticketno' => 'integer',
        'clientID' => 'string',
        'clientSystemID' => 'integer',
        'contractID' => 'string',
        'contractUID' => 'integer',
        'performaStatus' => 'integer',
        'BillProcessDate' => 'date',
        'SelectedForPerformaYN' => 'integer',
        'InvoiceNo' => 'string',
        'PerformaOpConfirmed' => 'integer',
        'PerformaFinanceConfirmed' => 'integer',
        'performaOpConfirmedBy' => 'string',
        'performaFinanceConfirmedBy' => 'string',
        'confirmedYN' => 'integer',
        'confirmedBy' => 'string',
        'confirmedByName' => 'string',
        'approvedYN' => 'integer',
        'approvedBy' => 'string',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'serviceLineCode' => 'string',
        'serviceLineSystemID' => 'integer',
        'serialNo' => 'integer',
        'billingCode' => 'string',
        'performaSerialNo' => 'integer',
        'performaCode' => 'string',
        'rentalType' => 'integer',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'modifiedUserID' => 'string',
        'performaMasterID' => 'integer',
        'isTrasportRental' => 'integer',
        'disableRental' => 'integer',
        'IsOpStbDaysFromMIT' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function ticketmaster()
    {
        return $this->belongsTo('App\Models\TicketMaster','Ticketno','ticketidAtuto');
    }

    public function performatemp(){
        return $this->hasMany('App\Models\PerformaTemp','performaInvoiceNo','PerformaInvoiceNo');
    }
    public function freebilling(){
        return $this->hasMany('App\Models\FreeBilling','performaInvoiceNo','PerformaInvoiceNo');
    }

    
}
