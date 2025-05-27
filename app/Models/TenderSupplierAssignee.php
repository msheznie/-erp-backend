<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

/**
 * @SWG\Definition(
 *      definition="TenderSupplierAssignee",
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
 *          property="registration_link_id",
 *          description="registration_link_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_assigned_id",
 *          description="supplier_assigned_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_email",
 *          description="supplier_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplier_name",
 *          description="supplier_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tender_master_id",
 *          description="tender_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderSupplierAssignee extends Model
{

        public $table = 'srm_tender_supplier_assignee';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
 
    public $fillable = [
        'company_id',
        'created_by',
        'registration_link_id',
        'supplier_assigned_id',
        'registration_number',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'mail_sent',
        'updated_by'
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
        'registration_link_id' => 'integer',
        'supplier_assigned_id' => 'integer',
        'supplier_email' => 'string',
        'supplier_name' => 'string',
        'registration_number' => 'string',
        'tender_master_id' => 'integer',
        'mail_sent' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'company_id' => 'required',
        'registration_link_id' => 'required'
    ];
 
    public function supplierAssigned(){ 
        return $this->hasOne('App\Models\SupplierAssigned', 'supplierAssignedID','supplier_assigned_id');
        
    }

    public static function getAssignSupplierCount($companyId, $id)
    {
        return TenderSupplierAssignee::where('company_id', $companyId)
            ->where('tender_master_id', $id)->count();
    }

    public static function getAssignSupplier($companySystemID, $tenderMasterId)
    {
        return TenderSupplierAssignee::select('id', 'tender_master_id', 'company_id', 'supplier_assigned_id')
            ->with(['supplierAssigned' => function ($q) {
                $q->select('supplierAssignedID', 'supplierCodeSytem');
                $q->with([
                    'supplierRegistrationLink' => function ($q) {
                        $q->select(
                            DB::raw('id as purchased_by'),
                            'name', 'supplier_master_id');
                    },
                ]);
            },
            ])
            ->where('company_id', $companySystemID)
            ->where('tender_master_id', $tenderMasterId)
            ->get();
    }
}
