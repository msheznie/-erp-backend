<?php

namespace App\Repositories;

use App\Models\StockReceiveDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class StockReceiveDetailsRefferedBackRepository
 * @package App\Repositories
 * @version November 29, 2018, 11:12 am UTC
 *
 * @method StockReceiveDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockReceiveDetailsRefferedBack find($id, $columns = ['*'])
 * @method StockReceiveDetailsRefferedBack first($columns = ['*'])
*/
class StockReceiveDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockReceiveDetailsID',
        'stockReceiveAutoID',
        'stockReceiveCode',
        'stockTransferAutoID',
        'stockTransferCode',
        'stockTransferDate',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodebBSSystemID',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'qty',
        'comments',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockReceiveDetailsRefferedBack::class;
    }
}
