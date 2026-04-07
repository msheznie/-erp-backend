<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @OA\Schema(
 *      schema="TenderFinalBids",
 *      required={""},
 *      @OA\Property(
 *          property="award",
 *          description="award",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="bid_id",
 *          description="bid_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="com_weightage",
 *          description="com_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="supplier_id",
 *          description="supplier_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tech_weightage",
 *          description="tech_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="total_weightage",
 *          description="total_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TenderFinalBids extends Model
{

    public $table = 'srm_tender_final_bids';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'award',
        'bid_id',
        'com_weightage',
        'status',
        'supplier_id',
        'tech_weightage',
        'tender_id',
        'total_weightage',
        'combined_ranking',
        'commercial_ranking'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'award' => 'boolean',
        'bid_id' => 'integer',
        'com_weightage' => 'float',
        'id' => 'integer',
        'status' => 'boolean',
        'supplier_id' => 'integer',
        'tech_weightage' => 'float',
        'tender_id' => 'integer',
        'total_weightage' => 'float',
        'commercial_ranking' => 'integer',
        'combined_ranking' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'award' => 'required',
        'bid_id' => 'required',
        'status' => 'required',
        'supplier_id' => 'required',
        'tender_id' => 'required'
    ];

    public function tender_master()
    {
        return $this->belongsTo('App\Models\TenderMaster', 'tender_id', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierRegistrationLink', 'supplier_id', 'id');
    }

    public function bid_submission_master() {
        return $this->belongsTo('App\Models\BidSubmissionMaster', 'bid_id', 'id');
    }

    public function supplierTenderNegotiation() {
        return $this->belongsTo('App\Models\SupplierTenderNegotiation', 'bid_id', 'srm_bid_submission_master_id');

    }
    public function scopeRanking($query)
    {
        return $query->select('supplier_id', 'combined_ranking')
            ->whereNotNull('combined_ranking')
            ->orderBy('combined_ranking')
            ->with(['supplier:id,name']);
    }
    public function scopeCommercial($query)
    {
        return $query->select('supplier_id', 'commercial_ranking', 'bid_id')
            ->whereNotNull('commercial_ranking')
            ->orderBy('commercial_ranking')
            ->with(['supplier:id,name', 'bid_submission_master:id,line_item_total']);
    }
    public static function getScheduleRankingData(int $tenderId, string $type = 'ranking')
    {
        $query = self::query()->where('tender_id', $tenderId);

        if ($type === 'ranking') {
            $query->ranking();
        } elseif ($type === 'commercial') {
            $query->commercial();
        } else {
            return [];
        }

        return $query->get();
    }
    public static function getScheduleAwardRankingRows(
        int $tenderId,
        int $isNegotiation,
        array $negotiationBidSubmissionMasterIds
    ): Collection {
        $query = self::selectRaw('srm_tender_final_bids.supplier_id, srm_tender_final_bids.combined_ranking')
            ->join('srm_bid_submission_master', 'srm_bid_submission_master.id', '=', 'srm_tender_final_bids.bid_id')
            ->where('srm_tender_final_bids.status', 1)
            ->where('srm_tender_final_bids.tender_id', $tenderId);

        if ($isNegotiation === 1) {
            $query->whereIn('srm_bid_submission_master.id', $negotiationBidSubmissionMasterIds);
        } else {
            $query->whereNotIn('srm_bid_submission_master.id', $negotiationBidSubmissionMasterIds);
        }

        return $query
            ->with(['supplier:id,name'])
            ->orderBy('srm_tender_final_bids.total_weightage', 'desc')
            ->get();
    }

    /**
     * Schedule-wise commercial ranking rows for supplier award visibility (matches getCommercialRanking negotiation filter).
     *
     * @param  array<int>  $negotiationBidSubmissionMasterIds
     */
    public static function getScheduleAwardCommercialRows(
        int $tenderId,
        int $isNegotiation,
        array $negotiationBidSubmissionMasterIds
    ): Collection {
        $query = self::selectRaw('srm_tender_final_bids.supplier_id, srm_tender_final_bids.commercial_ranking, srm_tender_final_bids.bid_id')
            ->join('srm_bid_submission_master', 'srm_bid_submission_master.id', '=', 'srm_tender_final_bids.bid_id')
            ->where('srm_tender_final_bids.tender_id', $tenderId);

        if ($isNegotiation === 1) {
            $query->whereIn('srm_bid_submission_master.id', $negotiationBidSubmissionMasterIds);
        } else {
            $query->whereNotIn('srm_bid_submission_master.id', $negotiationBidSubmissionMasterIds);
        }

        return $query
            ->with(['supplier:id,name', 'bid_submission_master:id,line_item_total'])
            ->orderBy('srm_tender_final_bids.com_weightage', 'desc')
            ->orderBy('srm_tender_final_bids.commercial_ranking')
            ->get();
    }
}
