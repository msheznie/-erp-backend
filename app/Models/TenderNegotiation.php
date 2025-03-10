<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderNegotiation extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // 0/1 - not started
    // 2 -started
    // 3 - completed
    
    public $fillable = [
        'srm_tender_master_id',
        'status',
        'approved_yn',
        'confirmed_yn',
        'confirmed_by',
        'confirmed_at',
        'comments',
        'started_by',
        'no_to_approve',
        'currencyId',
        'version'
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
        'approved_yn' => 'boolean',
        'confirmed_yn' => 'boolean',
        'started_by' => 'integer',
        'confirmed_by' => 'integer',
        'comments' => 'string',
        'confirmed_at' => 'date',
        'no_to_approve' => 'integer',
        'currencyId' => 'integer',
        'version' => 'integer'
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
    
    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmed_by', 'employeeSystemID');
    }

    public function area() {
        return $this->hasOne('App\Models\TenderNegotiationArea', 'tender_negotiation_id', 'id');
        
    }

    public function SupplierTenderNegotiation()
    {
        return $this->hasOne('App\Models\SupplierTenderNegotiation', 'tender_negotiation_id', 'id');
    }

    public function SupplierTenderNegotiationList()
    {
        return $this->hasMany('App\Models\SupplierTenderNegotiation', 'tender_negotiation_id', 'id');
    }

    public static function getTenderLatestNegotiations($tenderMasterId){
        return TenderNegotiation::select('id', 'version')
            ->where('srm_tender_master_id',$tenderMasterId)
            ->where('status',2)
            ->orderByDesc('version')
            ->first();
    }

    public static function tenderBidNegotiationList($tenderId, $isNegotiation)
    {
        if($isNegotiation){
            $latestNegotiation = self::getTenderLatestNegotiations($tenderId);
        }

        $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
            ->where('tender_id', $tenderId);

        if($isNegotiation){
            $tenderBidNegotiations = $tenderBidNegotiations->where('tender_negotiation_id', $latestNegotiation->id);
        }

        return $tenderBidNegotiations->get();
    }

}
