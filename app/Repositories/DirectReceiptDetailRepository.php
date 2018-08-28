<?php

namespace App\Repositories;

use App\Models\DirectReceiptDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DirectReceiptDetailRepository
 * @package App\Repositories
 * @version August 24, 2018, 12:12 pm UTC
 *
 * @method DirectReceiptDetail findWithoutFail($id, $columns = ['*'])
 * @method DirectReceiptDetail find($id, $columns = ['*'])
 * @method DirectReceiptDetail first($columns = ['*'])
*/
class DirectReceiptDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directReceiptAutoID',
        'companyID',
        'serviceLineCode',
        'glCode',
        'glCodeDes',
        'contractID',
        'comments',
        'DRAmountCurrency',
        'DDRAmountCurrencyER',
        'DRAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DirectReceiptDetail::class;
    }
}
