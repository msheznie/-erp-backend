<?php

namespace App\Repositories;

use App\Models\PayAdvanceReceiptDetail;
use App\Repositories\BaseRepository;

/**
 * Class PayAdvanceReceiptDetailRepository
 * @package App\Repositories
 * @version March 17, 2026, 2:44 pm +04
 *
 * @method PayAdvanceReceiptDetail findWithoutFail($id, $columns = ['*'])
 * @method PayAdvanceReceiptDetail find($id, $columns = ['*'])
 * @method PayAdvanceReceiptDetail first($columns = ['*'])
 */
class PayAdvanceReceiptDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'PayMasterAutoId',
        'advanceReceiptAutoID',
        'companySystemID',
        'advanceReceiptAmount',
        'advanceReceiptAmountLocal',
        'advanceReceiptAmountRpt',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PayAdvanceReceiptDetail::class;
    }
}

