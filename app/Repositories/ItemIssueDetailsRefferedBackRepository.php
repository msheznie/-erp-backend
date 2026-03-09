<?php

namespace App\Repositories;

use App\Models\ItemIssueDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class ItemIssueDetailsRefferedBackRepository
 * @package App\Repositories
 * @version December 3, 2018, 10:45 am UTC
 *
 * @method ItemIssueDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ItemIssueDetailsRefferedBack find($id, $columns = ['*'])
 * @method ItemIssueDetailsRefferedBack first($columns = ['*'])
*/
class ItemIssueDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemIssueDetailID',
        'itemIssueAutoID',
        'itemIssueCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'clientReferenceNumber',
        'qtyRequested',
        'qtyIssued',
        'comments',
        'convertionMeasureVal',
        'qtyIssuedDefaultMeasure',
        'localCurrencyID',
        'issueCostLocal',
        'issueCostLocalTotal',
        'reportingCurrencyID',
        'issueCostRpt',
        'issueCostRptTotal',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'maxQty',
        'minQty',
        'selectedForBillingOP',
        'selectedForBillingOPtemp',
        'opTicketNo',
        'del',
        'backLoad',
        'used',
        'grvDocumentNO',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'timesReferred',
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'p6',
        'p7',
        'p8',
        'p9',
        'p10',
        'p11',
        'p12',
        'p13',
        'pl10',
        'pl3',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemIssueDetailsRefferedBack::class;
    }
}
