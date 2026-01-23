<?php

namespace App\Repositories;

use App\Models\QuotationDetailsRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class QuotationDetailsRefferedbackRepository
 * @package App\Repositories
 * @version February 3, 2019, 11:02 am +04
 *
 * @method QuotationDetailsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method QuotationDetailsRefferedback find($id, $columns = ['*'])
 * @method QuotationDetailsRefferedback first($columns = ['*'])
*/
class QuotationDetailsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotationDetailsID',
        'quotationMasterID',
        'versionNo',
        'itemAutoID',
        'itemSystemCode',
        'itemDescription',
        'itemCategory',
        'defaultUOMID',
        'itemReferenceNo',
        'defaultUOM',
        'unitOfMeasureID',
        'unitOfMeasure',
        'conversionRateUOM',
        'requestedQty',
        'invoicedYN',
        'comment',
        'remarks',
        'unittransactionAmount',
        'discountPercentage',
        'discountAmount',
        'discountTotal',
        'transactionAmount',
        'companyLocalAmount',
        'companyReportingAmount',
        'customerAmount',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationDetailsRefferedback::class;
    }
}
