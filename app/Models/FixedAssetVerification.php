<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAssetVerification extends Model
{
    public $table = 'erp_fa_depmaster';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $guarded = [];
}
