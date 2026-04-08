<?php

namespace App\Models;

use Eloquent as Model;


class SrmTenderAwardingMemberEditLog extends Model
{

    public $table = 'srm_tender_awarding_members_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'version_id',
        'level_no',
        'tender_id',
        'user_id',
        'status',
        'awarding_remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'id' => 'integer',
        'version_id' => 'integer',
        'level_no' => 'integer',
        'tender_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'awarding_remarks' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'is_deleted' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'user_id');
    }

    public function tenderMaster()
    {
        return $this->belongsTo('App\Models\TenderMaster', 'tender_id', 'id');
    }

    public static function getLevelNo($id)
    {
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }

    public static function getAwardingMembersAmd($tenderID, $versionID)
    {
        return self::where('tender_id', $tenderID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }

    public static function getAmendRecords($versionID, $tenderMasterID, $onlyNullRecord)
    {
        return self::where('version_id', $versionID)
            ->where('tender_id', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecord, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecord, function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
    }

    public static function getAwardingMembers($tender_id, $versionID)
    {
        return self::where('tender_id', $tender_id)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->with('employee')
            ->get();
    }
    public static function getUserTenderAwardingMember($tenderID, $userID, $versionID){
        return self::where('tender_id', $tenderID)
            ->where('user_id', $userID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
}
