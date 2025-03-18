<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderBidNegotiation extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = 'srm_tender_bid_negotiation';
    protected $primaryKey = 'id';

    public $fillable = [
        'tender_id',
        'tender_negotiation_id',
        'bid_submission_master_id_old',
        'bid_submission_master_id_new',
        'bid_submission_code_old',
        'supplier_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tender_id' => 'integer',
        'tender_negotiation_id' => 'integer',
        'bid_submission_master_id_old' => 'integer',
        'bid_submission_master_id_new' => 'integer',
        'supplier_id' => 'integer',
        'bid_submission_code_old' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date'
    ];

    public function tender_negotiation_area(){
        return $this->hasOne('App\Models\TenderNegotiationArea', 'tender_negotiation_id', 'tender_negotiation_id');
    }

    public function BidSubmissionMaster() {
        return $this->belongsTo('App\Models\BidSubmissionMaster', 'bid_submission_master_id_new', 'id');
    }

    public static function getLatestNegotiationBidSubmissionMasterId($id)
    {
        return TenderBidNegotiation::select('bid_submission_master_id_new', 'bid_submission_master_id_old')->where('tender_negotiation_id', $id)->get()->toArray();
    }

    public static function getNegotiationIdByBidSubmissionMasterId($bidMasterId)
    {
        return TenderBidNegotiation::select('tender_negotiation_id')->where('bid_submission_master_id_new', $bidMasterId)->first();
    }
}
