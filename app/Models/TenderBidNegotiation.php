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
        'supplier_registration_id',
        'bid_submission_code_new',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tender_id' => 'integer',
        'tender_negotiation_id' => 'integer',
        'bid_submission_master_id_old' => 'integer',
        'supplier_registration_id' => 'integer',
        'bid_submission_master_id_new' => 'boolean',
        'bid_submission_code_old' => 'string',
        'bid_submission_code_new' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date'
    ];
}
