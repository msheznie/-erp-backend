<?php

namespace App\Models;

use Eloquent as Model;


class EmploymentTypeTransalation extends Model
{
    public $table = 'hrms_employment_types_translations';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'typeId',
        'languageCode',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'typeId' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'typeId' => 'required',
        'languageCode' => 'required',
        'description' => 'required'
    ];

     public function employeeType()
    {
        return $this->belongsTo(EmploymentType::class, 'typeId', 'id');
    }
}
