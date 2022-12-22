<?php

namespace App\Repositories;

use App\Models\PoDetailExpectedDeliveryDate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoDetailExpectedDeliveryDateRepository
 * @package App\Repositories
 * @version December 20, 2022, 2:25 pm +04
 *
 * @method PoDetailExpectedDeliveryDate findWithoutFail($id, $columns = ['*'])
 * @method PoDetailExpectedDeliveryDate find($id, $columns = ['*'])
 * @method PoDetailExpectedDeliveryDate first($columns = ['*'])
*/
class PoDetailExpectedDeliveryDateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'po_detail_auto_id',
        'expected_delivery_date',
        'allocated_qty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoDetailExpectedDeliveryDate::class;
    }
}
