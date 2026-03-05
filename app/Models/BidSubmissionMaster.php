<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BidSubmissionMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uuid",
 *          description="uuid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_registration_id",
 *          description="supplier_registration_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bid_sequence",
 *          description="bid_sequence",
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
class BidSubmissionMaster extends Model
{

    public $table = 'srm_bid_submission_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'uuid',
        'tender_id',
        'supplier_registration_id',
        'bid_sequence',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'doc_verifiy_yn',
        'doc_verifiy_by_emp',
        'doc_verifiy_date',
        'doc_verifiy_status',
        'doc_verifiy_comment',
        'bidSubmittedYN',
        'commercial_verify_status',
        'commercial_verify_at',
        'commercial_verify_by',
        'technical_verify_status',
        'technical_verify_at',
        'technical_verify_by',
        'technical_eval_remarks',
        'bidSubmissionCode',
        'serialNumber',
        'line_item_total',
        'tech_weightage',
        'comm_weightage',
        'total_weightage',
        'bidSubmittedDatetime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'integer',
        'tender_id' => 'integer',
        'supplier_registration_id' => 'integer',
        'bid_sequence' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'doc_verifiy_yn' => 'string',
        'doc_verifiy_status' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function SupplierRegistrationLink(){
        return $this->belongsTo(SupplierRegistrationLink::class, 'supplier_registration_id','id');
    }


    public function tender(){
        return $this->belongsTo(TenderMaster::class, 'tender_id','id');
    }

    public function bidSubmissionDetail(){
        return $this->hasMany(BidSubmissionDetail::class, 'bid_master_id','id');
    }

    public function BidDocumentVerification(){
        return $this->hasMany('App\Models\BidDocumentVerification', 'bis_submission_master_id', 'id');
    }

    public function TenderFinalBids() {
        return $this->belongsTo(TenderFinalBids::class, 'bid_id','id');
    }

    public function SupplierTenderNegotiation()
    {
        return $this->hasOne('App\Models\SupplierTenderNegotiation', 'bidSubmissionCode', 'bidSubmissionCode');
    }

    public function TenderBidNegotiation()
    {
        return $this->hasOne('App\Models\TenderBidNegotiation', 'bid_submission_master_id_new', 'id');
    }

    public function documents() {
        return $this->hasMany(DocumentAttachments::class, 'documentSystemCode','tender_id');

    }

    public static function getBidSubmissionData($tenderID, $bidID, $supplierId){
        return self::where([
            'id' => $bidID,
            'tender_id' => $tenderID,
            'supplier_registration_id' =>$supplierId
        ])->first();
    }

    public static function getSupplierIdsByTenderAndBidIds(int $tenderId, array $bidIds): array
    {
        if (empty($bidIds)) {
            return [];
        }
        return self::where('tender_id', $tenderId)
            ->whereIn('id', $bidIds)
            ->pluck('supplier_registration_id', 'id')
            ->toArray();
    }

    public static function checkTenderBidSubmitted($tender_id){
        return self::where('tender_id', $tender_id)->exists();
    }
    public static function getCommercialBidIds($tenderId, $isNegotiation)
    {
        $tender = TenderMaster::withCount([
            'criteriaDetails',
            'criteriaDetails as go_no_go_count' => function ($q) {
                $q->where('critera_type_id', 1);
            },
            'criteriaDetails as technical_count' => function ($q) {
                $q->where('critera_type_id', 2);
            },
            'DocumentAttachments as document_count' => function ($q) {
                $q->where('envelopType', 3);
            }
        ])->find($tenderId);

        if (!$tender) {
            return collect();
        }

        $negotiationIds = TenderNegotiation::tenderBidNegotiationList($tenderId, $isNegotiation)
            ->pluck('bid_submission_master_id_new')
            ->toArray();

        $query = self::query()
            ->where('status', 1)
            ->where('bidSubmittedYN', 1)
            ->where('srm_bid_submission_master.tender_id', $tenderId);

        if (!empty($negotiationIds)) {
            $query->when($isNegotiation == 1,
                fn($q) => $q->whereIn('srm_bid_submission_master.id', $negotiationIds),
                fn($q) => $q->whereNotIn('srm_bid_submission_master.id', $negotiationIds)
            );
        }

        if ($tender->technical_count == 0) {

            return $query
                ->where('doc_verifiy_status', 1)
                ->pluck('srm_bid_submission_master.id');
        }

        return $query
            ->selectRaw("
            srm_bid_submission_master.id,
            ROUND(SUM((srm_bid_submission_detail.eval_result/100)
            * srm_tender_master.technical_weightage),3) as weightage,
            srm_tender_master.technical_passing_weightage as passing_weightage
        ")
            ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
            ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
            ->where('srm_bid_submission_master.commercial_verify_status', 1)
            ->groupBy('srm_bid_submission_master.id')
            ->havingRaw('weightage >= passing_weightage')
            ->orderBy('srm_bid_submission_master.id')
            ->pluck('id');
    }

}
