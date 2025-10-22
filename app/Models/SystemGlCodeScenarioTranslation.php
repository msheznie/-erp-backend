<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemGlCodeScenarioTranslation extends Model
{
    public $table = 'system_gl_code_scenario_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'system_gl_code_scenario_id',
        'languageCode',
        'description',
        'purpose',
        'transaction'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'system_gl_code_scenario_id' => 'integer',
        'languageCode' => 'string',
        'description' => 'string',
        'purpose' => 'string',
        'transaction' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Get the system gl code scenario that owns the translation.
     */
    public function systemGlCodeScenario()
    {
        return $this->belongsTo(SystemGlCodeScenario::class, 'system_gl_code_scenario_id', 'id');
    }
}
