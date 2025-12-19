<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSpecification extends Model
{
    public $table = 'item_specifications';
    
    public $timestamps = false;

    protected $primaryKey  = 'id';


    public $fillable = [
        'item_id',
        'sub_cat_id',
        'html',
    ];

    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'item_id' => 'integer',
        'sub_cat_id' => 'integer',
        'html' => 'string',
    ];

    public function item(){
        return $this->hasOne('App\Models\ItemMaster','itemCodeSystem','item_id');
    }

    public function category(){
        return $this->hasOne('App\Models\FinanceItemCategorySub','itemCategorySubID','sub_cat_id');
    }

}
