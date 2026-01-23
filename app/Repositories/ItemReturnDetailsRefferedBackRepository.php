<?php

namespace App\Repositories;

use App\Models\ItemReturnDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class ItemReturnDetailsRefferedBackRepository
 * @package App\Repositories
 * @version December 6, 2018, 5:35 am UTC
 *
 * @method ItemReturnDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ItemReturnDetailsRefferedBack find($id, $columns = ['*'])
 * @method ItemReturnDetailsRefferedBack first($columns = ['*'])
*/
class ItemReturnDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemReturnDetailID',
        'itemReturnAutoID',
        'itemReturnCode',
        'issueCodeSystem',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'qtyIssued',
        'convertionMeasureVal',
        'qtyIssuedDefaultMeasure',
        'comments',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'qtyFromIssue',
        'selectedForBillingOP',
        'selectedForBillingOPtemp',
        'opTicketNo',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemReturnDetailsRefferedBack::class;
    }
}
