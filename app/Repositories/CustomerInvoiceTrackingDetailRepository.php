<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceTrackingDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceTrackingDetailRepository
 * @package App\Repositories
 * @version February 10, 2020, 7:57 am +04
 *
 * @method CustomerInvoiceTrackingDetail findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceTrackingDetail find($id, $columns = ['*'])
 * @method CustomerInvoiceTrackingDetail first($columns = ['*'])
*/
class CustomerInvoiceTrackingDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerInvoiceTrackingID',
        'companyID',
        'companySystemID',
        'customerID',
        'custInvoiceDirectAutoID',
        'bookingInvCode',
        'bookingDate',
        'customerInvoiceNo',
        'customerInvoiceDate',
        'invoiceDueDate',
        'contractID',
        'PerformaInvoiceNo',
        'wanNO',
        'PONumber',
        'rigNo',
        'wellNo',
        'amount',
        'confirmedDate',
        'customerApprovedYN',
        'customerApprovedDate',
        'customerApprovedByEmpID',
        'customerApprovedByEmpSystemID',
        'customerApprovedByEmpName',
        'customerApprovedByDate',
        'approvedAmount',
        'customerRejectedYN',
        'customerRejectedDate',
        'customerRejectedByEmpID',
        'customerRejectedByEmpSystemID',
        'customerRejectedByEmpName',
        'customerRejectedByDate',
        'rejectedAmount',
        'remarks',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceTrackingDetail::class;
    }
}
