<?php

namespace App\Repositories;

use App\Models\StockAdjustmentDetailsRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockAdjustmentDetailsRefferedBackRepository
 * @package App\Repositories
 * @version February 6, 2019, 11:30 am +04
 *
 * @method StockAdjustmentDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockAdjustmentDetailsRefferedBack find($id, $columns = ['*'])
 * @method StockAdjustmentDetailsRefferedBack first($columns = ['*'])
*/
class StockAdjustmentDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockAdjustmentDetailsAutoID',
        'stockAdjustmentAutoID',
        'stockAdjustmentAutoIDCode',
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
        'noQty',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockAdjustmentDetailsRefferedBack::class;
    }
}
