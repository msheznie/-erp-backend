<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PerformaTemp",
 *      required={""},
 *      @SWG\Property(
 *          property="myAutoID",
 *          description="myAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaMasterID",
 *          description="performaMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="myStdTitle",
 *          description="myStdTitle",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractid",
 *          description="contractid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaInvoiceNo",
 *          description="performaInvoiceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sumofsumofStandbyAmount",
 *          description="sumofsumofStandbyAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="TicketNo",
 *          description="TicketNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="myTicketNo",
 *          description="myTicketNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientID",
 *          description="clientID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaFinanceConfirmed",
 *          description="performaFinanceConfirmed",
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
 *          property="performaFinanceConfirmedBy",
 *          description="performaFinanceConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="stdGLcode",
 *          description="stdGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="proformaComment",
 *          description="proformaComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDiscount",
 *          description="isDiscount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="discountDescription",
 *          description="discountDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DiscountPercentage",
 *          description="DiscountPercentage",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class PerformaTemp extends Model
{

    public $table = 'performatemp';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'myAutoID';


    public $fillable = [
        'performaMasterID',
        'myStdTitle',
        'companyID',
        'contractid',
        'performaInvoiceNo',
        'sumofsumofStandbyAmount',
        'TicketNo',
        'myTicketNo',
        'clientID',
        'performaDate',
        'performaFinanceConfirmed',
        'PerformaOpConfirmed',
        'performaFinanceConfirmedBy',
        'performaOpConfirmedDate',
        'performaFinanceConfirmedDate',
        'stdGLcode',
        'sortOrder',
        'timestamp',
        'proformaComment',
        'isDiscount',
        'discountDescription',
        'DiscountPercentage'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'myAutoID' => 'integer',
        'performaMasterID' => 'integer',
        'myStdTitle' => 'string',
        'companyID' => 'string',
        'contractid' => 'string',
        'performaInvoiceNo' => 'integer',
        'sumofsumofStandbyAmount' => 'float',
        'TicketNo' => 'integer',
        'myTicketNo' => 'string',
        'clientID' => 'string',
        'performaFinanceConfirmed' => 'integer',
        'PerformaOpConfirmed' => 'integer',
        'performaFinanceConfirmedBy' => 'string',
        'stdGLcode' => 'string',
        'sortOrder' => 'integer',
        'proformaComment' => 'string',
        'isDiscount' => 'integer',
        'discountDescription' => 'string',
        'DiscountPercentage' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
