<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderNegotiation extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // 1 - not started
    // 2 -pending
    // 3 - completed
    
    public $fillable = [
        'srm_tender_master_id',
        'status',
        'approved_yn',
        'confirmed_yn',
        'confirmed_by',
        'confirmed_at',
        'started_by'
    ];

        /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'srm_tender_master_id' => 'integer',
        'status' => 'integer',
        'approved_yn' => 'integer',
        'confirmed_yn' => 'integer',
        'started_by' => 'integer',
        'confirmed_by' => 'integer',
        'confirmed_at' => 'date'
    ];

        /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'srm_tender_master_id' => 'required',
    ];


    public function tenderMaster()
    {
        return $this->hasOne('App\Models\TenderMaster', 'id', 'srm_tender_master_id');
    }

    public function supplierTenderNegotiation() {
        return $this->hasOne('App\Models\SupplierTenderNegotiation', 'id', 'srm_tender_master_id');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmed_by', 'employeeSystemID');
    }

}
