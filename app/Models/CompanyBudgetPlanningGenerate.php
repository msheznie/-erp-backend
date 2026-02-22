<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stores getBudgetGenerateDetails result. One DB row per result row.
 * Enables returning cached data on next request and retrieving a single row by row_id.
 */
class CompanyBudgetPlanningGenerate extends Model
{
    protected $table = 'generate_budget_plans';

    protected $fillable = [
        'company_budget_planning_id',
        'row_id',
        'payload',
        'is_generated',
    ];

    protected $casts = [
        'company_budget_planning_id' => 'integer',
        'row_index' => 'integer',
        'payload' => 'array',
        'is_generated' => 'boolean',
    ];

    public function companyBudgetPlanning()
    {
        return $this->belongsTo(CompanyBudgetPlanning::class, 'company_budget_planning_id', 'id');
    }
}
