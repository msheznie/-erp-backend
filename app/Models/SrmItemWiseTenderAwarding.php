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
        'is_awarded',
        'award_email_sent',
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
        'is_awarded' => 'integer',
        'award_email_sent' => 'boolean',
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

    public function scopeForTenderNegotiation($query, $tenderId, $isNegotiation)
    {
        return $query->where('tender_id', $tenderId)->where('is_negotiation', (int) $isNegotiation);
    }

    public static function clearAwardForTenderNegotiation($tenderId, $isNegotiation)
    {
        return self::where('tender_id', $tenderId)->where('is_negotiation', (int) $isNegotiation)->update(['award' => 0]);
    }

    public static function getAwardMapForTender($tenderId, $isNegotiation)
    {
        $existing = self::forTenderNegotiation($tenderId, $isNegotiation)->get();
        $awardMap = [];
        foreach ($existing as $row) {
            $key = $row->boq_item_id ? 'boq_' . $row->boq_item_id : 'main_' . $row->bid_format_detail_id;
            if ($row->award) {
                $awardMap[$key] = ['bid_id' => $row->bid_id, 'supplier_id' => $row->supplier_id];
            }
        }
        return $awardMap;
    }

    public static function createOrUpdateAwarding(array $match, array $attributes)
    {
        return self::updateOrCreate($match, $attributes);
    }

    /**
     * Get awarded rows for tender (optionally for one supplier). For Send Email / listing.
     */
    public static function getAwardedRowsForTender(int $tenderId, int $isNegotiation, ?int $supplierId = null)
    {
        $query = self::forTenderNegotiation($tenderId, $isNegotiation)->where('award', 1);
        if ($supplierId !== null) {
            $query->where('supplier_id', $supplierId);
        }
        return $query;
    }

    /**
     * Count total awarded lines and count lines marked is_awarded for tender.
     */
    public static function getAwardedCountsForTender(int $tenderId, int $isNegotiation): array
    {
        $total = self::forTenderNegotiation($tenderId, $isNegotiation)->where('award', 1)->count();
        $awarded = self::forTenderNegotiation($tenderId, $isNegotiation)->where('award', 1)->where('is_awarded', 1)->count();
        return ['total' => $total, 'awarded' => $awarded];
    }

    /**
     * Mark all awarded lines for a supplier as is_awarded = 1.
     */
    public static function markSupplierLinesAsAwarded(int $tenderId, int $isNegotiation, int $supplierId): int
    {
        return self::getAwardedRowsForTender($tenderId, $isNegotiation, $supplierId)->update(['is_awarded' => 1]);
    }

    /**
     * Mark award_email_sent = 1 for all rows of a supplier.
     */
    public static function markAwardEmailSentForSupplier(int $tenderId, int $isNegotiation, int $supplierId): int
    {
        return self::getAwardedRowsForTender($tenderId, $isNegotiation, $supplierId)->update(['award_email_sent' => 1]);
    }
}
