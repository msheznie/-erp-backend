<?php

namespace App\Models;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractTimeMaterialConsumption extends Model
{
    use SoftDeletes;
    public $table = 'cm_time_material_consumption';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['id', 'boq_id'];
    protected $dates = ['deleted_at'];

    public $fillable = [
        'uuid',
        'contract_id',
        'item',
        'description',
        'min_quantity',
        'max_quantity',
        'price',
        'quantity',
        'uom_id',
        'amount',
        'boq_id',
        'currency_id',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contract_id' => 'integer',
        'item' => 'string',
        'description' => 'string',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'price' => 'float',
        'quantity' => 'integer',
        'uom_id' => 'integer',
        'amount' => 'float',
        'boq_id' => 'integer',
        'currency_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function units()
    {
        return $this->belongsTo(Unit::class, 'uom_id', 'UnitID');
    }

    public static function getAllTimeMaterialConsumption($contractID)
    {
        return self::select('uuid', 'item', 'description', 'min_quantity', 'max_quantity', 'price',
            'amount', 'quantity', 'uom_id')
            ->with([
                "units" => function ($q)
                {
                    $q->select('UnitID', 'UnitDes');
                },
            ])
            ->where('contract_id', $contractID)
            ->get();
    }
}
