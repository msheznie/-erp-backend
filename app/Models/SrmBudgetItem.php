<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SrmBudgetItem extends Model
{
    protected $table = 'srm_budget_items';

    protected $fillable = [
       'item_id',
       'item_name',
       'budget_amount',
       'is_active',
       'company_id'
    ];

    public $timestamps = true;

    public function tenderBudgetItems()
    {
        return $this->hasMany(SrmTenderBudgetItem::class, 'item_id', 'id');
    }
    public function tenderBudgetItemsLog()
    {
        return $this->hasMany(TenderBudgetItemEditLog::class, 'item_id', 'id');
    }

    public static function getSrmBudgetItemList($tenderMasterId, $companySystemID, $editOrAmend, $versionID){
        return self::select('srm_budget_items.id as id', 'item_name AS itemName')
            ->where('company_id', $companySystemID)
            ->when($editOrAmend, function ($q) use ($tenderMasterId, $versionID) {
                $q->whereHas('tenderBudgetItemsLog', function ($query) use ($tenderMasterId, $versionID) {
                    $query->where('tender_id', $tenderMasterId)
                        ->where('version_id', $versionID)
                        ->where('is_deleted', 0);
                });

            })
            ->when(!$editOrAmend, function ($q) use ($tenderMasterId) {
                $q->whereHas('tenderBudgetItems', function ($query) use ($tenderMasterId) {
                    $query->where('tender_id', $tenderMasterId);
                });
            })->get();
    }
    public static function getSrmBudgetItem($itemID, $companyID){
        return self::select('budget_amount')
            ->where('id', $itemID)
            ->where('company_id', $companyID)
            ->first();
    }
}
