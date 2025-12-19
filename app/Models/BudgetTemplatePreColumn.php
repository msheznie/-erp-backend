<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTemplatePreColumn extends Model
{
    public $table = 'budget_template_pre_columns';
    public $primaryKey = 'preColumnID';

    public $fillable = [
        'columnName',
        'slug',
        'columnType',
        'description',
        'isSystemPredefined'
    ];

    protected $casts = [
        'preColumnID' => 'integer',
        'columnType' => 'integer',
        'isSystemPredefined' => 'boolean'
    ];

    public static $rules = [
        'columnName' => 'required|string|max:255|unique:budget_template_pre_columns,columnName',
        'slug' => 'nullable|string|max:255|unique:budget_template_pre_columns,slug',
        'columnType' => 'required|integer|in:1,2,4,5',
        'description' => 'nullable|string',
        'isSystemPredefined' => 'boolean'
    ];

    /**
     * Get the column type label
     */
    public function getColumnTypeLabel()
    {
        $types = [
            1 => 'Text',
            2 => 'Number',
            4 => 'Formula',
            5 => 'Date'
        ];

        return $types[$this->columnType] ?? 'Unknown';
    }

    /**
     * Relationship with template columns
     */
    public function templateColumns()
    {
        return $this->hasMany(BudgetTemplateColumn::class, 'preColumnID', 'preColumnID');
    }
} 