<?php

namespace App\Repositories;

use App\Models\StockCountDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class StockCountDetailsRefferedBackRepository
 * @package App\Repositories
 * @version June 14, 2021, 2:02 pm +04
 *
 * @method StockCountDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockCountDetailsRefferedBack find($id, $columns = ['*'])
 * @method StockCountDetailsRefferedBack first($columns = ['*'])
*/
class StockCountDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockCountDetailsAutoID',
        'stockCountAutoID',
        'stockCountAutoIDCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'partNumber',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'systemQty',
        'noQty',
        'adjustedQty',
        'comments',
        'currentWacLocalCurrencyID',
        'currentWaclocal',
        'currentWacRptCurrencyID',
        'currentWacRpt',
        'wacAdjLocal',
        'wacAdjRptER',
        'wacAdjRpt',
        'wacAdjLocalER',
        'currenctStockQty',
        'timesReferred',
        'timestamp',
        'updatedFlag'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockCountDetailsRefferedBack::class;
    }
}
