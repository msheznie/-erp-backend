<?php

namespace App\Repositories;

use App\Models\StockAdjustmentDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockAdjustmentDetailsRepository
 * @package App\Repositories
 * @version August 20, 2018, 11:57 am UTC
 *
 * @method StockAdjustmentDetails findWithoutFail($id, $columns = ['*'])
 * @method StockAdjustmentDetails find($id, $columns = ['*'])
 * @method StockAdjustmentDetails first($columns = ['*'])
*/
class StockAdjustmentDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'currenctStockQty',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockAdjustmentDetails::class;
    }
}
