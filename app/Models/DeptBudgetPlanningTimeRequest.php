<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeptBudgetPlanningTimeRequest extends Model
{
    protected $table = 'dept_budget_planning_time_requests';

    protected $fillable = [
        'department_budget_planning_id',
        'request_code',
        'current_submission_date',
        'new_time',
        'date_of_request',
        'reason_for_extension',
        'status',
        'review_comments',
        'reviewed_by',
        'reviewed_at',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'current_submission_date',
        'new_time',
        'date_of_request',
        'reviewed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the department budget planning that owns the time request.
     */
    public function departmentBudgetPlanning()
    {
        return $this->belongsTo(DepartmentBudgetPlanning::class, 'department_budget_planning_id');
    }

    /**
     * Get the company system ID for this time request.
     */
    public function getCompanySystemID()
    {
        return $this->departmentBudgetPlanning->masterBudgetPlannings->companySystemID ?? null;
    }

    /**
     * Get the attachments for the time request.
     */
    public function attachments()
    {
        return $this->hasMany(DeptBudgetPlanningTimeRequestAttachment::class, 'time_request_id');
    }

    /**
     * Get the user who created the request.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who reviewed the request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who last updated the request.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for filtering by department budget planning ID
     */
    public function scopeForBudgetPlanning($query, $budgetPlanningId)
    {
        return $query->where('department_budget_planning_id', $budgetPlanningId);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 1:
                return 'Time Requested';
            case 2:
                return 'Approved';
            case 3:
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get attachment count
     */
    public function getAttachmentCountAttribute()
    {
        return $this->attachments()->count();
    }

    /**
     * Get created by name
     */
    public function getCreatedByNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Unknown';
    }

    /**
     * Check if new time is set
     *
     * @return bool
     */
    public function hasNewTime()
    {
        return !is_null($this->new_time);
    }

    /**
     * Get the time extension duration in days
     *
     * @return int|null
     */
    public function getExtensionDaysAttribute()
    {
        if (!$this->hasNewTime() || !$this->current_submission_date) {
            return null;
        }

        return $this->current_submission_date->diffInDays($this->new_time, false);
    }

    /**
     * Get formatted new time
     *
     * @param string $format
     * @return string|null
     */
    public function getFormattedNewTimeAttribute($format = 'Y-m-d H:i:s')
    {
        return $this->new_time ? $this->new_time->format($format) : null;
    }
}
