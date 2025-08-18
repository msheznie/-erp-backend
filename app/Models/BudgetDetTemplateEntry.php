<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetDetTemplateEntry extends Model
{
    use SoftDeletes;

    protected $table = 'budget_det_template_entries';
    protected $primaryKey = 'entryID';

    protected $fillable = [
        'budget_detail_id',
        'rowNumber',
        'created_by',
        'timestamp'
    ];

    protected $casts = [
        'budget_detail_id' => 'integer',
        'rowNumber' => 'integer',
        'created_by' => 'integer',
        'timestamp' => 'datetime'
    ];

    protected $dates = [
        'timestamp',
        'created_at',
        'updated_at',
        'deleted_at'
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
     * Scope to get entries by row number
     */
    public function scopeByRowNumber($query, $rowNumber)
    {
        return $query->where('rowNumber', $rowNumber);
    }
} 