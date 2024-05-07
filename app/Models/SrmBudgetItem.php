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
}
