<?php

namespace App\Repositories;

use App\Models\DirectReceiptDetailsRefferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class DirectReceiptDetailsRefferedHistoryRepository
 * @package App\Repositories
 * @version November 21, 2018, 10:52 am UTC
 *
 * @method DirectReceiptDetailsRefferedHistory findWithoutFail($id, $columns = ['*'])
 * @method DirectReceiptDetailsRefferedHistory find($id, $columns = ['*'])
 * @method DirectReceiptDetailsRefferedHistory first($columns = ['*'])
*/
class DirectReceiptDetailsRefferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directReceiptDetailsID',
        'directReceiptAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'glSystemID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'contractID',
        'contractUID',
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
        return DirectReceiptDetailsRefferedHistory::class;
    }
}
