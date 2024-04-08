<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSystemMapping extends Model
{

    public $table = 'document_system_mapping';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'documentSystemId',
        'documentId',
        'thirdPartySystemId',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemId' => 'integer',
        'documentId' => 'integer',
        'thirdPartySystemId' => 'integer',
        'timestamp' => 'datetime'
    ];


    public function third_party_system() {
        $this->hasOne('App\Models\ThirdPartySystems','thirdPartySystemId');
    }

}
