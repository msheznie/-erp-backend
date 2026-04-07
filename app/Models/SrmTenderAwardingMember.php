<?php

namespace App\Models;

use Eloquent as Model;

class SrmTenderAwardingMember extends Model
{
    public $table = 'srm_tender_awarding_members';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'tender_id',
        'user_id',
        'status',
        'awarding_remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'awarding_remarks' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    public static $rules = [];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'user_id');
    }

    public function tenderMaster()
    {
        return $this->belongsTo('App\Models\TenderMaster', 'tender_id', 'id');
    }

    public static function getAwardingMembers($tenderID)
    {
        return self::where('tender_id', $tenderID)
            ->with('employee')
            ->get();
    }
    public static function getAwardingMember($tenderID, $userID)
    {
        return self::select('id', 'tender_id', 'user_id', 'status', 'awarding_remarks', 'created_by', 'created_at')
            ->where('tender_id', $tenderID)
            ->where('user_id', $userID)
            ->first();
    }
    public static function getApprovedAwardingMembers($tenderID)
    {
        return self::select('id', 'tender_id', 'user_id', 'status', 'awarding_remarks', 'created_by', 'created_at')
            ->where('tender_id', $tenderID)
            ->where('status', 1)
            ->count();
    }
}
