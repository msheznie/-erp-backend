<?php

namespace App\Repositories;

use App\Models\QuotationDetails;
use App\Repositories\BaseRepository;

/**
 * Class QuotationDetailsRepository
 * @package App\Repositories
 * @version January 22, 2019, 2:04 pm +04
 *
 * @method QuotationDetails findWithoutFail($id, $columns = ['*'])
 * @method QuotationDetails find($id, $columns = ['*'])
 * @method QuotationDetails first($columns = ['*'])
*/
class QuotationDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotationMasterID',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationDetails::class;
    }
}
