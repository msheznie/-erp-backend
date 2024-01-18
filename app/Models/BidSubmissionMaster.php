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
}
