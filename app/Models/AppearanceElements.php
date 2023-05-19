<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

class AppearanceElements extends Model
{
    public $table = 'appearance_elements';
    protected $primaryKey = 'id';


    public $fillable = [
        'elementName'
    ];

    protected $casts = [
        'id' => 'integer',
        'elementName' => 'string'
    ];


}
