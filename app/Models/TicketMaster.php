<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TicketMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="ticketidAtuto",
 *          description="ticketidAtuto",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketNo",
 *          description="ticketNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketMonth",
 *          description="ticketMonth",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketYear",
 *          description="ticketYear",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractRefNo",
 *          description="contractRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="regName",
 *          description="regName",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="regNo",
 *          description="regNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientID",
 *          description="clientID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketCategory",
 *          description="ticketCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fieldName",
 *          description="fieldName",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fieldType",
 *          description="fieldType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wellNo",
 *          description="wellNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wellType",
 *          description="wellType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
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
 *      ),
 *      @SWG\Property(
 *          property="ticketStatus",
 *          description="ticketStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketStatusEmpID",
 *          description="ticketStatusEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketStatusComment",
 *          description="ticketStatusComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BillingStatus",
 *          description="BillingStatus",
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
 *          property="confirmedComment",
 *          description="confirmedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="JobAcheived",
 *          description="JobAcheived",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jobNetworkNo",
 *          description="jobNetworkNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryUnitAssetID",
 *          description="primaryUnitAssetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jobSupervisor",
 *          description="jobSupervisor",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Temperature",
 *          description="Temperature",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="Depth",
 *          description="Depth",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="Totalhourloac",
 *          description="Totalhourloac",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="TotalOperatingHours",
 *          description="TotalOperatingHours",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="jobScheduledYNBM",
 *          description="jobScheduledYNBM",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jobScheduledEmpIDBM",
 *          description="jobScheduledEmpIDBM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobScheduledCommentBM",
 *          description="jobScheduledCommentBM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobStartedYNBM",
 *          description="jobStartedYNBM",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jobStartedEmpIDBM",
 *          description="jobStartedEmpIDBM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobStartedCommentBM",
 *          description="jobStartedCommentBM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobEndYNSup",
 *          description="jobEndYNSup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jobEndEmpIDSup",
 *          description="jobEndEmpIDSup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobEndCommentSup",
 *          description="jobEndCommentSup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketTypeMaster",
 *          description="ticketTypeMaster",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketType",
 *          description="ticketType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedBillingYN",
 *          description="selectedBillingYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="processSelectTemp",
 *          description="processSelectTemp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="estimatedServiceValue",
 *          description="estimatedServiceValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="estimatedProductValue",
 *          description="estimatedProductValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="revenueYear",
 *          description="revenueYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="revenueMonth",
 *          description="revenueMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketServiceValue",
 *          description="ticketServiceValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="ticketProductValue",
 *          description="ticketProductValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="ticketNature",
 *          description="ticketNature",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketClientSerial",
 *          description="ticketClientSerial",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyComment",
 *          description="companyComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientComment",
 *          description="clientComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="opDept",
 *          description="opDept",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poNumber",
 *          description="poNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tempPerformaMasID",
 *          description="tempPerformaMasID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tempPerformaCode",
 *          description="tempPerformaCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cancelledYN",
 *          description="cancelledYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketCancelledDesc",
 *          description="ticketCancelledDesc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EngID",
 *          description="EngID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketManulNo",
 *          description="ticketManulNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractUID",
 *          description="contractUID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="oldNoUpdate",
 *          description="oldNoUpdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="JobFailure",
 *          description="JobFailure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isFail",
 *          description="isFail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerRep",
 *          description="customerRep",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyRep",
 *          description="companyRep",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerRepContact",
 *          description="customerRepContact",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="country",
 *          description="country",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isWeb",
 *          description="isWeb",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="assginBaseManager",
 *          description="assginBaseManager",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assginSuperviser",
 *          description="assginSuperviser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="callout",
 *          description="callout",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceEntry",
 *          description="serviceEntry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="batchNo",
 *          description="batchNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="callOutDate",
 *          description="callOutDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="sqauditCategoryID",
 *          description="sqauditCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="querySentYN",
 *          description="querySentYN",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="querySentDate",
 *          description="querySentDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="querySentBy",
 *          description="querySentBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeApprovedYN",
 *          description="financeApprovedYN",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeApprovedDate",
 *          description="financeApprovedDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="financeApprovedBy",
 *          description="financeApprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDeleted",
 *          description="isDeleted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deletedBy",
 *          description="deletedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deletedComment",
 *          description="deletedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobDescID",
 *          description="jobDescID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="secondComments",
 *          description="secondComments",
 *          type="string"
 *      )
 * )
 */
class TicketMaster extends Model
{

    public $table = 'ticketmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'createdDateTime';
    protected $primaryKey  = 'ticketidAtuto';


    public $fillable = [
        'ticketNo',
        'ticketMonth',
        'ticketYear',
        'contractRefNo',
        'regName',
        'regNo',
        'companyID',
        'clientID',
        'ticketCategory',
        'serviceLine',
        'fieldName',
        'fieldType',
        'wellNo',
        'wellType',
        'comments',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'ticketStatus',
        'ticketStatusEmpID',
        'ticketStatusDate',
        'ticketStatusComment',
        'BillingStatus',
        'confirmedYN',
        'confirmedBy',
        'confrmedDate',
        'confirmedComment',
        'JobAcheived',
        'jobNetworkNo',
        'documentID',
        'serialNo',
        'primaryUnitAssetID',
        'jobSupervisor',
        'Temperature',
        'Depth',
        'timeBaseLeftLocation',
        'TimeDateArrive',
        'TimedateRigup',
        'Timedatejobstra',
        'Timedatejobend',
        'Timedateleaveloc',
        'Totalhourloac',
        'TotalOperatingHours',
        'jobScheduledYNBM',
        'jobScheduledEmpIDBM',
        'jobScheduledDateBM',
        'jobScheduledCommentBM',
        'jobStartedYNBM',
        'jobStartedEmpIDBM',
        'jobStartedDateBM',
        'jobStartedCommentBM',
        'jobEndYNSup',
        'jobEndEmpIDSup',
        'jobEndDateSup',
        'jobEndCommentSup',
        'ticketTypeMaster',
        'ticketType',
        'selectedBillingYN',
        'processSelectTemp',
        'estimatedServiceValue',
        'estimatedProductValue',
        'revenueYear',
        'revenueMonth',
        'ticketServiceValue',
        'ticketProductValue',
        'ticketNature',
        'ticketClientSerial',
        'companyComment',
        'clientComment',
        'opDept',
        'poNumber',
        'tempPerformaMasID',
        'tempPerformaCode',
        'cancelledYN',
        'ticketCancelledDesc',
        'EngID',
        'ticketManulNo',
        'contractUID',
        'oldNoUpdate',
        'JobFailure',
        'isFail',
        'customerRep',
        'companyRep',
        'customerRepContact',
        'country',
        'isWeb',
        'assginBaseManager',
        'assginSuperviser',
        'callout',
        'rigClosedDate',
        'serviceEntry',
        'submissionDate',
        'batchNo',
        'callOutDate',
        'sqauditCategoryID',
        'querySentYN',
        'querySentDate',
        'querySentBy',
        'financeApprovedYN',
        'financeApprovedDate',
        'financeApprovedBy',
        'isDeleted',
        'deletedBy',
        'deletedDate',
        'deletedComment',
        'jobDescID',
        'secondComments',
        'companySystemID',
        'clientSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ticketidAtuto' => 'integer',
        'ticketNo' => 'string',
        'ticketMonth' => 'string',
        'ticketYear' => 'string',
        'contractRefNo' => 'string',
        'regName' => 'integer',
        'regNo' => 'string',
        'companyID' => 'string',
        'clientID' => 'string',
        'ticketCategory' => 'integer',
        'serviceLine' => 'string',
        'fieldName' => 'integer',
        'fieldType' => 'integer',
        'wellNo' => 'string',
        'wellType' => 'integer',
        'comments' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'ticketStatus' => 'integer',
        'ticketStatusEmpID' => 'string',
        'ticketStatusComment' => 'string',
        'BillingStatus' => 'string',
        'confirmedYN' => 'integer',
        'confirmedBy' => 'string',
        'confirmedComment' => 'string',
        'JobAcheived' => 'integer',
        'jobNetworkNo' => 'string',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'primaryUnitAssetID' => 'integer',
        'jobSupervisor' => 'string',
        'Temperature' => 'float',
        'Depth' => 'float',
        'Totalhourloac' => 'float',
        'TotalOperatingHours' => 'float',
        'jobScheduledYNBM' => 'integer',
        'jobScheduledEmpIDBM' => 'string',
        'jobScheduledCommentBM' => 'string',
        'jobStartedYNBM' => 'integer',
        'jobStartedEmpIDBM' => 'string',
        'jobStartedCommentBM' => 'string',
        'jobEndYNSup' => 'integer',
        'jobEndEmpIDSup' => 'string',
        'jobEndCommentSup' => 'string',
        'ticketTypeMaster' => 'string',
        'ticketType' => 'integer',
        'selectedBillingYN' => 'integer',
        'processSelectTemp' => 'integer',
        'estimatedServiceValue' => 'float',
        'estimatedProductValue' => 'float',
        'revenueYear' => 'integer',
        'revenueMonth' => 'integer',
        'ticketServiceValue' => 'float',
        'ticketProductValue' => 'float',
        'ticketNature' => 'integer',
        'ticketClientSerial' => 'integer',
        'companyComment' => 'string',
        'clientComment' => 'string',
        'opDept' => 'integer',
        'poNumber' => 'string',
        'tempPerformaMasID' => 'integer',
        'tempPerformaCode' => 'string',
        'cancelledYN' => 'integer',
        'ticketCancelledDesc' => 'string',
        'EngID' => 'string',
        'ticketManulNo' => 'string',
        'contractUID' => 'integer',
        'oldNoUpdate' => 'integer',
        'JobFailure' => 'integer',
        'isFail' => 'string',
        'customerRep' => 'string',
        'companyRep' => 'string',
        'customerRepContact' => 'string',
        'country' => 'string',
        'isWeb' => 'boolean',
        'assginBaseManager' => 'string',
        'assginSuperviser' => 'string',
        'callout' => 'integer',
        'serviceEntry' => 'string',
        'batchNo' => 'integer',
        'callOutDate' => 'date',
        'sqauditCategoryID' => 'integer',
        'querySentYN' => 'string',
        'querySentDate' => 'date',
        'querySentBy' => 'string',
        'financeApprovedYN' => 'string',
        'financeApprovedDate' => 'date',
        'financeApprovedBy' => 'string',
        'isDeleted' => 'integer',
        'deletedBy' => 'string',
        'deletedComment' => 'string',
        'jobDescID' => 'integer',
        'secondComments' => 'string',
        'companySystemID' => 'integer',
        'clientSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function field()
    {
        return $this->belongsTo('App\Models\FieldMaster','fieldName','FieldID');
    }

    public function rig()
    {
        return $this->belongsTo('App\Models\RigMaster','regName','idrigmaster');
    }
    
}
