<?php

namespace App\Models;

use Eloquent as Model;

class EmployeeLanguage extends Model
{
    public $table = 'employees_languages';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public $fillable = [
        'employeeID',
        'languageID',
        'timeStamp'
    ];

    protected $casts = [
        'id' => 'integer',
        'employeeID' => 'integer',
        'languageID' => 'integer',
        'timeStamp' => 'datetime'
    ];

    public function language()
    {
        return $this->hasOne('App\Models\ERPLanguageMaster','languageID','languageID');
    }}
