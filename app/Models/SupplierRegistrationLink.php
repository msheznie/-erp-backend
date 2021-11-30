<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierRegistrationLink extends Model
{
    public $table = 'srm_supplier_registration_link';

    protected $primaryKey  = 'id';

    public $fillable = [
        'uuid',
        'supplier_master_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'supplier_master_id' => 'integer',
        'uuid' => 'string',
        ];

    public function supplier(){
        return $this->belongsTo(SupplierMaster::class, 'supplier_master_id','supplierCodeSystem');
    }
}
