<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetDetTemplateEntryData extends Model
{
    use SoftDeletes;

    protected $table = 'budget_det_template_entry_data';
    protected $primaryKey = 'dataID';

    protected $fillable = [
        'entryID',
        'templateColumnID',
        'value',
        'timestamp'
    ];

    protected $casts = [
        'entryID' => 'integer',
        'templateColumnID' => 'integer',
        'value' => 'string',
        'timestamp' => 'datetime'
    ];

    protected $dates = [
        'timestamp',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the entry that owns this data
     */
    public function entry()
    {
        return $this->belongsTo(BudgetDetTemplateEntry::class, 'entryID', 'entryID');
    }

    /**
     * Get the template column for this data
     */
    public function templateColumn()
    {
        return $this->belongsTo(BudgetTemplateColumn::class, 'templateColumnID', 'templateColumnID');
    }

    /**
     * Scope to get data by entry ID
     */
    public function scopeForEntry($query, $entryID)
    {
        return $query->where('entryID', $entryID);
    }

    /**
     * Scope to get data by template column ID
     */
    public function scopeForTemplateColumn($query, $templateColumnID)
    {
        return $query->where('templateColumnID', $templateColumnID);
    }
} 