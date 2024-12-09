<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *      schema="SRMTenderPaymentProof",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="uuid",
 *          description="uuid",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="serial_no",
 *          description="serial_no",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="document_system_id",
 *          description="document_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="document_id",
 *          description="document_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="document_code",
 *          description="document_code",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_uuid",
 *          description="tender_uuid",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="srm_supplier_uuid",
 *          description="srm_supplier_uuid",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="confirmed_yn",
 *          description="confirmed_yn",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmed_by_emp_system_id",
 *          description="confirmed_by_emp_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmed_by_emp_id",
 *          description="confirmed_by_emp_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="confirmed_by_name",
 *          description="confirmed_by_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="confirmed_date",
 *          description="confirmed_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="approved_yn",
 *          description="approved_yn",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approved_date",
 *          description="approved_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="approved_emp_system_id",
 *          description="approved_emp_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approved_by_emp_id",
 *          description="approved_by_emp_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="approved_by_emp_name",
 *          description="approved_by_emp_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SRMTenderPaymentProof extends Model
{

    public $table = 'srm_tender_payment_proof';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'uuid',
        'serial_no',
        'document_system_id',
        'document_id',
        'document_code',
        'company_id',
        'tender_id',
        'srm_supplier_id',
        'confirmed_yn',
        'confirmed_by_emp_system_id',
        'confirmed_by_emp_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_emp_system_id',
        'approved_by_emp_id',
        'approved_by_emp_name',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'serial_no' => 'integer',
        'document_system_id' => 'integer',
        'document_id' => 'string',
        'document_code' => 'string',
        'company_id' => 'integer',
        'tender_id' => 'integer',
        'srm_supplier_id' => 'integer',
        'confirmed_yn' => 'integer',
        'confirmed_by_emp_system_id' => 'integer',
        'confirmed_by_emp_id' => 'string',
        'confirmed_by_name' => 'string',
        'confirmed_date' => 'datetime',
        'approved_yn' => 'integer',
        'approved_date' => 'datetime',
        'approved_emp_system_id' => 'integer',
        'approved_by_emp_id' => 'string',
        'approved_by_emp_name' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'uuid' => 'required',
        'serial_no' => 'required',
        'document_system_id' => 'required',
        'company_id' => 'required',
        'tender_uuid' => 'required',
        'srm_supplier_uuid' => 'required',
        'confirmed_yn' => 'required',
        'approved_yn' => 'required',
        'refferedBackYN' => 'required',
        'timesReferred' => 'required',
        'RollLevForApp_curr' => 'required'
    ];

    public static function fetchProofDocumentSerial($companyCode,$documentCode)
    {
        $lastSerial = self::orderByDesc('serial_no')->value('serial_no');
        $nextSerial = $lastSerial ? $lastSerial + 1 : 1;
        $documentCode = ($companyCode . '/' . $documentCode . str_pad($nextSerial, 6, '0', STR_PAD_LEFT));

        return [
            'nextSerial' => $nextSerial,
            'documentCode' => $documentCode,
        ];
    }

    public static function getPaymentProofData($documentSystemId, $companyId, $tenderId, $srmSupplierId)
    {
        return self::select('id','uuid','document_code','tender_id','srm_supplier_id', 'confirmed_yn','refferedBackYN')
            ->with(['documentAttachment' => function($query) use ($documentSystemId, $companyId){
                $query->select('attachmentID','documentSystemID','documentSystemCode','attachmentDescription','path',
                'originalFileName','myFileName','companySystemID')
                ->where('documentSystemID',$documentSystemId)
                ->where('companySystemID',$companyId);
            }])
            ->where('tender_id', $tenderId)
            ->where('srm_supplier_id', $srmSupplierId)
            ->first();
    }

    public function documentAttachment()
    {
       return $this->hasMany('App\Models\DocumentAttachments', 'documentSystemCode', 'id');
    }

    public static function getPaymentProofDataByUuid($uuid)
    {
        return self::select('id','uuid','document_code','tender_id','srm_supplier_id','company_id','document_system_id')
            ->with(['srmSupplier' => function($query) {
                $query->select('id','name','email');
            }])
            ->where('uuid', $uuid)
            ->first();
    }

    public function srmSupplier()
    {
        return $this->hasOne(SupplierRegistrationLink::class, 'id', 'srm_supplier_id');
    }

    public static function getTenderPaymentReview($companyId,$empId)
    {
        return DB::table('erp_documentapproved as da')
            ->join('srm_tender_payment_proof as pf', 'pf.id', '=', 'da.documentSystemCode')
            ->join('srm_tender_master as tm', 'tm.id', '=', 'pf.tender_id')
            ->join('srm_tender_type as tt', 'tt.id', '=', 'tm.tender_type_id')
            ->join('srm_envelop_type as et', 'et.id', '=', 'tm.envelop_type_id')
            ->join('currencymaster as cm', 'cm.currencyID', '=', 'tm.currency_id')
            ->join('employeesdepartments as emd', function ($join) {
                $join->on('emd.employeeGroupID', '=', 'da.approvalGroupID')
                    ->on('da.documentSystemID', '=', 'emd.documentSystemID')
                    ->on('da.companySystemID', '=', 'emd.companySystemID');
            })
            ->where([
                ['da.companySystemID', '=', $companyId],
                ['emd.documentSystemID', '=', 127],
                ['emd.departmentSystemID', '=', 66],
                ['emd.companySystemID', '=', $companyId],
                ['emd.employeeSystemID', '=', $empId],
                ['emd.isActive', '=', 1],
                ['emd.removedYN', '=', 0],
            ])
            ->groupBy('tm.id')
            ->select([
                'tm.uuid',
                'tm.tender_code',
                'tm.title',
                'tm.description',
                'tt.name as selection',
                'et.name as envelope',
                'cm.CurrencyCode as currencyCode',
            ]);
    }

    public static function getSupplierWiseData($companyId,$empId, $tenderId)
    {
        return DB::table('erp_documentapproved as da')
            ->join('srm_tender_payment_proof as pf', 'pf.id', '=', 'da.documentSystemCode')
            ->join('srm_tender_master as tm', 'tm.id', '=', 'pf.tender_id')
            ->join('srm_tender_type as tt', 'tt.id', '=', 'tm.tender_type_id')
            ->join('srm_envelop_type as et', 'et.id', '=', 'tm.envelop_type_id')
            ->join('currencymaster as cm', 'cm.currencyID', '=', 'tm.currency_id')
            ->join('srm_supplier_registration_link as srl', 'srl.id', '=', 'pf.srm_supplier_id')
            ->join('employeesdepartments as emd', function ($join) {
                $join->on('emd.employeeGroupID', '=', 'da.approvalGroupID')
                    ->on('da.documentSystemID', '=', 'emd.documentSystemID')
                    ->on('da.companySystemID', '=', 'emd.companySystemID');
            })
            ->where([
                ['da.companySystemID', '=', $companyId],
                ['emd.documentSystemID', '=', 127],
                ['emd.departmentSystemID', '=', 66],
                ['emd.companySystemID', '=', $companyId],
                ['emd.employeeSystemID', '=', $empId],
                ['emd.isActive', '=', 1],
                ['emd.removedYN', '=', 0],
                ['tm.id', '=', $tenderId],
            ])
            ->groupBy('da.documentSystemCode')
            ->select([
                 'pf.uuid',
                 'pf.id as pfCode',
                 'pf.document_system_id as docSysCode',
                  DB::raw('IF(pf.RollLevForApp_curr = da.rollLevelOrder, true, false) as level'),
                 'srl.name as supplierName',
	             'srl.registration_number as supplierCr',
	             'pf.confirmed_date as submittedDate',
	             'pf.confirmed_yn',
	             'pf.approved_yn',
                 'da.documentApprovedID as documentApCode',
                 'pf.refferedBackYN'
            ]);
    }
}
