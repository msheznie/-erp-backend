<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeptBudgetPlanningTimeRequestAttachment extends Model
{

    protected $table = 'dept_budget_planning_time_request_attachments';

    protected $fillable = [
        'time_request_id',
        'file_name',
        'original_file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the time request that owns the attachment.
     */
    public function timeRequest()
    {
        return $this->belongsTo(DeptBudgetPlanningTimeRequest::class, 'time_request_id');
    }

    /**
     * Get the user who uploaded the attachment.
     */
    public function uploader()
    {
        return $this->belongsTo(\App\User::class, 'uploaded_by');
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}