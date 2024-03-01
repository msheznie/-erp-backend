<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SrmTenderBudgetItem extends Model
{
    protected $table = 'srm_tender_budget_items';

    protected $fillable = [
       'item_id',
       'tender_id',
       'budget_amount',
       'created_at'
    ];

    public $timestamps = false;

    public function budgetItem()
    {
        return $this->belongsTo(SrmBudgetItem::class, 'item_id', 'item_id');
    }
}
