<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SrmTenderBudgetItem extends Model
{
    protected $table = 'srm_tender_budget_items';

    protected $fillable = [
       'item_id',
       'tender_id',
       'budget_amount',
       'created_at'
    ];

    public $timestamps = true;

    public function budgetItem()
    {
        return $this->belongsTo(SrmBudgetItem::class, 'item_id', 'item_id');
    }

    public static function getTenderBudgetItemForAmd($tenderID){
        return self::where('tender_id', $tenderID)->get();
    }

    public static function getExistingBudgetItem($itemId, $tenderMasterId){
        return self::where('item_id', $itemId)->where('tender_id', $tenderMasterId)->first();
    }

    public static function getTenderBudgetItems($tableName, $tenderMasterId, $companySystemID, $editOrAmendRequest, $versionID){
        return DB::table($tableName)
            ->select(
                DB::raw("CAST($tableName.item_id AS UNSIGNED) as id"),
                DB::raw("CONCAT(srm_budget_items.item_name, ' - ', $tableName.budget_amount) AS item_name")
            )
            ->leftJoin('srm_budget_items', "$tableName.item_id", '=', 'srm_budget_items.id')
            ->where("$tableName.tender_id", $tenderMasterId)
            ->where('srm_budget_items.is_active', 1)
            ->where('srm_budget_items.company_id', $companySystemID)
            ->when($editOrAmendRequest, function ($q) use ($tableName, $versionID) {
                $q->where("$tableName.version_id", $versionID)
                    ->where("$tableName.is_deleted", 0);
            })
            ->unionAll(
                DB::table('srm_budget_items')
                    ->select(
                        DB::raw("CAST(srm_budget_items.id AS UNSIGNED) as id"),
                        DB::raw("CONCAT(srm_budget_items.item_name, ' - ', srm_budget_items.budget_amount) AS item_name")
                    )
                    ->where('srm_budget_items.is_active', 1)
                    ->where('srm_budget_items.company_id', $companySystemID)
                    ->whereNotIn('srm_budget_items.id', function ($query) use ($tenderMasterId, $editOrAmendRequest, $versionID, $tableName) {
                        $query->select('item_id')
                            ->from($tableName)
                            ->where('tender_id', $tenderMasterId);

                        if ($editOrAmendRequest) {
                            $query->where('version_id', $versionID)
                                ->where('is_deleted', 0);
                        }
                    })
            )
            ->get();
    }
}
