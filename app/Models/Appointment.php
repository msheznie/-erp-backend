<?php

namespace App\Models;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Appointment",
 *      required={""},
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="slot_detail_id",
 *          description="slot_detail_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_id",
 *          description="supplier_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tenat_id",
 *          description="tenat_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class Appointment extends Model
{
    use Compoships;
    public $table = 'appointment';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'company_id',
        'created_by',
        'slot_detail_id',
        'status',
        'supplier_id',
        'tenat_id',
        'document_id',
        'document_system_id',
        'serial_no',
        'primary_code',
        'confirmed_by_emp_id',
        'confirmedByName',
        'confirmedByEmpID',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'RollLevForApp_curr',
        'timesReferred',
        'confirmed_yn',
        'refferedBackYN',
        'cancelYN',
        'canceledDate',
        'canceledByEmpId',
        'canceledReason',
        'canceledByName',
        'grv_create_yn',
        'grv'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'slot_detail_id' => 'integer',
        'status' => 'integer',
        'supplier_id' => 'integer',
        'tenat_id' => 'integer',
        'document_id' => 'varchar',
        'document_system_id' => 'integer',
        'serial_no' => 'integer',
        'primary_code' => 'varchar',
        'confirmed_by_emp_id' => 'integer',
        'confirmedByName' => 'varchar',
        'confirmedByEmpID' => 'varchar',
        'confirmed_date' => 'datetime',
        'approved_yn' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_emp_name' => 'varchar',
        'approved_by_emp_id' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'timesReferred' => 'integer',
        'confirmed_yn' => 'integer',
        'refferedBackYN' => 'integer',
        'cancelYN' => 'integer',
        'canceledDate' => 'datetime',
        'canceledByEmpId' => 'integer',
        'canceledReason' => 'varchar',
        'canceledByName' => 'varchar'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function detail()
    {
        return $this->hasMany('App\Models\AppointmentDetails', 'appointment_id', 'id');
    }
    public function created_by()
    {
        return $this->hasOne('App\Models\SupplierMaster', 'supplierCodeSystem', 'created_by');
    }
    public function documentApproved()
    {
        return $this->hasOne('App\Models\DocumentApproved',['documentSystemID', 'documentSystemCode', 'rollLevelOrder'], ['document_system_id', 'id', 'RollLevForApp_curr']);
    }

    public function slot_detail()
    {
        return $this->hasOne('App\Models\SlotDetails', 'id', 'slot_detail_id');
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\SupplierMaster', 'supplierCodeSystem', 'supplier_id');
    }

    public function attachment()
    {
        return $this->hasMany('App\Models\DocumentAttachments',['documentSystemID', 'documentSystemCode'], ['document_system_id', 'id']);
    }

    public function grv()
    {
        return $this->hasOne('App\Models\GRVMaster', 'deliveryAppoinmentID', 'id');
    }

    public function invoice()
    {
        return $this->hasOne('App\Models\BookInvSuppMaster', 'deliveryAppoinmentID', 'id');
    }

    public function getDeliveryAppointmentDetails($appointmentId)
    {
        return AppointmentDetails::select('appointment_id','po_master_id')
        ->where('appointment_id', $appointmentId)
        ->with(['po_master' => function ($q) {
        $q->select('purchaseOrderID','serviceLineSystemID');
        }])->get();
    }

}
