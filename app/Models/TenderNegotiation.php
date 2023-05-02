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
        'started_by' => 'integer'
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

}
