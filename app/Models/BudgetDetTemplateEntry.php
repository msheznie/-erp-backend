<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetDetTemplateEntry extends Model
{

    protected $table = 'budget_det_template_entries';
    protected $primaryKey = 'entryID';

    protected $fillable = [
        'budget_detail_id',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'budget_detail_id' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the budget detail that owns this entry
     */
    public function budgetDetail()
    {
        return $this->belongsTo(DepartmentBudgetPlanningDetail::class, 'budget_detail_id', 'id');
    }

    /**
     * Get the user who created this entry
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the entry data for this entry
     */
    public function entryData()
    {
        return $this->hasMany(BudgetDetTemplateEntryData::class, 'entryID', 'entryID');
    }

    /**
     * Scope to get entries by budget detail ID
     */
    public function scopeForBudgetDetail($query, $budgetDetailId)
    {
        return $query->where('budget_detail_id', $budgetDetailId);
    }

    /**
     * Scope to order entries by row number
     */
    public function scopeOrderByEntryID($query)
    {
        return $query->orderBy('entryID', 'asc');
    }
} 
