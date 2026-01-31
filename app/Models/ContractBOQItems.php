<?php

namespace App\Models;
use App\Helpers\inventory;
use Eloquent as Model;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ContractBOQItems extends Model
{
    public $table = 'cm_contract_boq_items';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];
    protected $hidden = ['id'];




    public $fillable = [
        'uuid',
        'contractId',
        'itemId',
        'description',
        'minQty',
        'maxQty',
        'qty',
        'price',
        'origin',
        'companyId',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractId' => 'integer',
        'itemId' => 'integer',
        'description' => 'string',
        'minQty' => 'integer',
        'maxQty' => 'integer',
        'qty' => 'integer',
        'origin' => 'integer',
        'price' => 'float',
        'companyId' => 'integer',
        'created_by' => 'string',
        'updated_by' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'itemId', 'itemCodeSystem');
    }

    public function boqItem()
    {
        return $this->belongsTo(TenderBoqItems::class, 'itemId', 'id');
    }

    public static function getBoqItems($companyId,$uuid,$id, $origin = 1)
    {
        return self::select('uuid', 'minQty', 'maxQty', 'qty', 'companyId', 'itemId','price', 'origin')
            ->with([
                'itemMaster' => function ($q) {
                    $q->select(
                        'itemCodeSystem',
                        'unit',
                        'itemDescription',
                        'primaryCode'
                    )
                        ->with([
                            'unit' => function ($q) {
                                $q->select('UnitID', 'UnitShortCode');
                            },
                            'itemAssigned' => function ($q) {
                                $q->select('itemCodeSystem', 'wacValueLocalCurrencyID')
                                    ->with([
                                        'local_currency' => function ($q) {
                                            $q->select('currencyID', 'DecimalPlaces');
                                        }
                                    ]);
                            }
                        ]);
                },
                'boqItem' => function ($q)
                {
                    $q->select('id', 'uom', 'description', 'item_name');
                },
                'boqItem.unit' => function ($query)
                {
                    $query->select('UnitID', 'UnitShortCode');
                }])
            ->where('companyId', $companyId)
            ->where('contractId', $id)
            ->when($origin > 0, function ($q) use ($origin)
            {
                $q->where('origin', $origin);
            })
            ->orderBy('id', 'desc')
            ->get();
    }
}
