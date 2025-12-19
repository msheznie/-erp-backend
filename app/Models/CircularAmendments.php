<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircularAmendments extends Model
{
    public $table = 'srm_circular_amendments';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'tender_id',
        'circular_id',
        'amendment_id',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'circular_id' => 'integer',
        'amendment_id' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function document_attachments()
    {
        return $this->hasOne('App\Models\DocumentAttachments', 'attachmentID', 'amendment_id');
    }

    public static function getCircularAmendmentForAmd($tender_id){
        return self::where('tender_id', $tender_id)->get();
    }
    public static function getCircularAmendmentByID($circularID){
        return self::select('amendment_id')->where('circular_id', $circularID)->get()->toArray();
    }
    public static function getAmendmentAttachment($amendmentID, $circularID, $tenderMasterId)
    {
        return self::where('amendment_id', $amendmentID)
            ->where('tender_id', $tenderMasterId)
            ->where('circular_id', $circularID)
            ->first();
    }
    public static function getAllCircularAmendments($circularID){
        return self::where('circular_id', $circularID)->get();
    }
    public static function checkAmendmentIsUsedInCircular($amendmentID, $tenderMasterId){
        return self::where('amendment_id',  $amendmentID)->where('tender_id', $tenderMasterId)->count();
    }
}
