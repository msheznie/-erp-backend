<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetTemplateComment extends Model
{
    use SoftDeletes;

    protected $table = 'budget_template_comments';
    protected $primaryKey = 'commentID';

    protected $fillable = [
        'budget_detail_id',
        'user_id',
        'comment_text',
        'parent_comment_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the budget detail that owns the comment
     */
    public function budgetDetail()
    {
        return $this->belongsTo(DepartmentBudgetPlanningDetail::class, 'budget_detail_id', 'id');
    }

    /**
     * Get the user who created the comment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the parent comment (for replies)
     */
    public function parentComment()
    {
        return $this->belongsTo(BudgetTemplateComment::class, 'parent_comment_id', 'commentID');
    }

    /**
     * Get the replies to this comment
     */
    public function replies()
    {
        return $this->hasMany(BudgetTemplateComment::class, 'parent_comment_id', 'commentID');
    }

    /**
     * Scope to get comments for a specific budget detail
     */
    public function scopeForBudgetDetail($query, $budgetDetailId)
    {
        return $query->where('budget_detail_id', $budgetDetailId);
    }

    /**
     * Scope to get top-level comments (not replies)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_comment_id');
    }

    /**
     * Scope to get comments by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the author name
     */
    public function getAuthorNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Check if comment can be edited by user
     */
    public function canEditBy($userId)
    {
        return $this->user_id == $userId;
    }
}
