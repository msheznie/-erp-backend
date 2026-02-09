<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Item-wise tender awarding: one row per (tender, item, bid/supplier).
 * For evaluation_type_id = 1 (Item wise).
 */
class SrmItemWiseTenderAwarding extends Model
{
    use SoftDeletes;

    public $table = 'srm_item_wise_tender_awarding';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'tender_id',
        'bid_format_detail_id',
        'boq_item_id',
        'bid_id',
        'supplier_id',
        'bid_amount',
        'system_pick',
        'award',
        'is_negotiation',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'bid_format_detail_id' => 'integer',
        'boq_item_id' => 'integer',
        'bid_id' => 'integer',
        'supplier_id' => 'integer',
        'bid_amount' => 'decimal:3',
        'system_pick' => 'integer',
        'award' => 'boolean',
        'is_negotiation' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function tender_master()
    {
        return $this->belongsTo(TenderMaster::class, 'tender_id', 'id');
    }

    public function bid_submission_master()
    {
        return $this->belongsTo(BidSubmissionMaster::class, 'bid_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierRegistrationLink::class, 'supplier_id', 'id');
    }
}
