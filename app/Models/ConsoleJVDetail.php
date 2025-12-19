<?php
/**
 * =============================================
 * -- File Name : ConsoleJVDetail.php
 * -- Project Name : ERP
 * -- Module Name :  General Ledger
 * -- Author : Mubashir
 * -- Create date : 06 - March 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ConsoleJVDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="consoleJvDetailAutoID",
 *          description="consoleJvDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consoleJvMasterAutoId",
 *          description="consoleJvMasterAutoId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jvDetailAutoID",
 *          description="jvDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jvMasterAutoId",
 *          description="jvMasterAutoId",
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
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
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
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glAccountSystemID",
 *          description="glAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glAccount",
 *          description="glAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glAccountDescription",
 *          description="glAccountDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyER",
 *          description="currencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="debitAmount",
 *          description="debitAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditAmount",
 *          description="creditAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localDebitAmount",
 *          description="localDebitAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptDebitAmount",
 *          description="rptDebitAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCreditAmount",
 *          description="localCreditAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptCreditAmount",
 *          description="rptCreditAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="consoleType",
 *          description="consoleType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      )
 * )
 */
class ConsoleJVDetail extends Model
{

    public $table = 'erp_consolejvdetail';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'consoleJvDetailAutoID';

    public $fillable = [
        'consoleJvMasterAutoId',
        'jvDetailAutoID',
        'jvMasterAutoId',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentCode',
        'glDate',
        'glAccountSystemID',
        'glAccount',
        'glAccountDescription',
        'comments',
        'currencyID',
        'currencyER',
        'debitAmount',
        'creditAmount',
        'localDebitAmount',
        'rptDebitAmount',
        'localCreditAmount',
        'rptCreditAmount',
        'consoleType',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'consoleJvDetailAutoID' => 'integer',
        'consoleJvMasterAutoId' => 'integer',
        'jvDetailAutoID' => 'integer',
        'jvMasterAutoId' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentCode' => 'string',
        'glAccountSystemID' => 'integer',
        'glAccount' => 'string',
        'glAccountDescription' => 'string',
        'comments' => 'string',
        'currencyID' => 'integer',
        'currencyER' => 'float',
        'debitAmount' => 'float',
        'creditAmount' => 'float',
        'localDebitAmount' => 'float',
        'rptDebitAmount' => 'float',
        'localCreditAmount' => 'float',
        'rptCreditAmount' => 'float',
        'consoleType' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->where('companySystemID',  $type);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfMaster($query, $consoleJvMasterAutoId)
    {
        return $query->where('consoleJvMasterAutoId',  $consoleJvMasterAutoId);
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\ConsoleJVMaster', 'consoleJvMasterAutoId', 'consoleJvMasterAutoId');
    }



}
