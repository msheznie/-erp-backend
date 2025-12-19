<?php

namespace App\Repositories;

use App\Models\TenderPaymentDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderPaymentDetailRepository
 * @package App\Repositories
 * @version December 23, 2024, 8:07 am +04
 *
 * @method TenderPaymentDetail findWithoutFail($id, $columns = ['*'])
 * @method TenderPaymentDetail find($id, $columns = ['*'])
 * @method TenderPaymentDetail first($columns = ['*'])
*/
class TenderPaymentDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'srm_supplier_id',
        'payment_method',
        'payment_id',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderPaymentDetail::class;
    }
}
