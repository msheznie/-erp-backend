<?php
/**
 * =============================================
 * -- File Name : ExampleTableTemplateTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Example Table Template Translation
 * -- Author : System Generated
 * -- Create date : 13- September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ExampleTableTemplateTranslation
 * @package App\Models
 * @version September 13, 2025, 4:00 pm UTC
 *
 * @property integer example_table_template_id
 * @property string languageCode
 * @property string data
 */
class ExampleTableTemplateTranslation extends Model
{
    //use SoftDeletes;

    public $table = 'example_table_template_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'example_table_template_id',
        'languageCode',
        'data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'example_table_template_id' => 'integer',
        'languageCode' => 'string',
        'data' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'example_table_template_id' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'data' => 'required|string'
    ];

    /**
     * Get the example table template that owns the translation.
     */
    public function exampleTableTemplate()
    {
        return $this->belongsTo(ExampleTableTemplate::class, 'example_table_template_id', 'id');
    }
}
