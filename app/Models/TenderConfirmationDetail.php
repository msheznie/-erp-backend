<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderConfirmationDetail extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'srm_tender_confirmation_details';

    public $fillable = [
        'tender_id',
        'reference_id',
        'module',
        'action_by',
        'action_at',
        'comment'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'reference_id' => 'integer',
        'module' => 'integer',
        'action_by' => 'integer',
        'action_at' => 'string',
        'comment' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tender_id' => 'required',
        'module' => 'required|integer|min:1|max:12',
    ];

    /**
     * Relationship to TenderMaster
     */
    public function tenderMaster()
    {
        return $this->belongsTo('App\Models\TenderMaster', 'tender_id', 'id');
    }

    /**
     * Relationship to Employee (who performed the action)
     */
    public function actionByEmployee()
    {
        return $this->belongsTo('App\Models\Employee', 'action_by', 'employeeSystemID');
    }

    /**
     * Module constants
     */
    const MODULE_GO_NO_GO = 1;
    const MODULE_TECHNICAL_EVAL = 2;
    const MODULE_COMMERCIAL_REVIEW = 3;
    const MODULE_LINE_ITEM = 4;
    const MODULE_COMMERCIAL_RANKING = 5;
    const MODULE_COMBINED_RANKING = 6;
    const MODULE_NEGOTIATION = 7;
    const MODULE_AWARDING = 8;
    const MODULE_AWARDED = 9;
    const MODULE_BID_OPENING_APPROVAL = 10;
    const MODULE_COMMITTEE_BID_OPENING_APPROVAL = 11;
    const MODULE_AWARDING_APPROVAL = 12;

    public static function getTenderConfirmationDetails($tender_id, $module, $empIds){
        return self::where('tender_id', $tender_id)
            ->where('module', $module)
            ->whereIn('reference_id', $empIds)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('reference_id')
            ->keyBy('reference_id');
    }

    public static function getLatestByTenderModuleReference(int $tenderId, int $module, int $referenceId)
    {
        return self::where('tender_id', $tenderId)
            ->where('module', $module)
            ->where('reference_id', $referenceId)
            ->orderByDesc('id')
            ->first();
    }

    public static function getByTenderModuleReferences(int $tenderId, int $module, array $referenceIds)
    {
        return self::where('tender_id', $tenderId)
            ->where('module', $module)
            ->whereIn('reference_id', $referenceIds)
            ->orderBy('id', 'asc')
            ->get();
    }
}
