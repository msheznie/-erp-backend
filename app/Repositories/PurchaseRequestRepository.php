<?php

namespace App\Repositories;

use App\Models\PurchaseRequest;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class PurchaseRequestRepository
 * @package App\Repositories
 * @version March 26, 2018, 7:00 am UTC
 *
 * @method PurchaseRequest findWithoutFail($id, $columns = ['*'])
 * @method PurchaseRequest find($id, $columns = ['*'])
 * @method PurchaseRequest first($columns = ['*'])
*/
class PurchaseRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyJobID',
        'serialNumber',
        'purchaseRequestCode',
        'comments',
        'location',
        'priority',
        'deliveryLocation',
        'PRRequestedDate',
        'docRefNo',
        'invoiceNumber',
        'currency',
        'buyerEmpID',
        'buyerEmpSystemID',
        'buyerEmpName',
        'buyerEmpEmail',
        'supplierCodeSystem',
        'supplierName',
        'supplierAddress',
        'supplierTransactionCurrencyID',
        'supplierCountryID',
        'financeCategory',
        'PRConfirmedYN',
        'PRConfirmedBy',
        'PRConfirmedBySystemID',
        'PRConfirmedDate',
        'isActive',
        'approved',
        'approvedDate',
        'timesReferred',
        'prClosedYN',
        'prClosedComments',
        'prClosedByEmpID',
        'prClosedDate',
        'cancelledYN',
        'cancelledByEmpID',
        'cancelledByEmpName',
        'cancelledComments',
        'cancelledDate',
        'selectedForPO',
        'selectedForPOByEmpID',
        'supplyChainOnGoing',
        'poTrackID',
        'RollLevForApp_curr',
        'hidePOYN',
        'hideByEmpID',
        'hideByEmpName',
        'hideDate',
        'hideComments',
        'PreviousBuyerEmpID',
        'delegatedDate',
        'delegatedComments',
        'fromWeb',
        'wo_status',
        'doc_type',
        'refferedBackYN',
        'isAccrued',
        'budgetYear',
        'prBelongsYear',
        'budgetBlockYN',
        'budgetBlockByEmpID',
        'budgetBlockByEmpEmailID',
        'checkBudgetYN',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'approval_remarks'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseRequest::class;
    }

    public function purchaseRequestListQuery($request, $input, $search = '', $serviceLineSystemID) {

        $purchaseRequests = PurchaseRequest::where('companySystemID', $input['companyId']);


        if (array_key_exists('requestReview', $input)) {
            if ($input['requestReview'] == 1) {
                $purchaseRequests->where('cancelledYN', 0);
                //->where('approved', -1);
            }
        } else {
            $purchaseRequests = $purchaseRequests->where('documentSystemID', $input['documentId']);
        }

        if(isset($request['isFromPortal']) && $request['isFromPortal'] ){
            $purchaseRequests = $purchaseRequests->where('createdUserSystemID', $request['createdUserSystemID']);
        }

        $purchaseRequests = $purchaseRequests->with(['created_by' => function ($query) {
        }, 'priority' => function ($query) {

        }, 'location' => function ($query) {

        }, 'segment' => function ($query) {

        }, 'financeCategory' => function ($query) {

        }, 'location_pdf','priority_pdf']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseRequests->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('cancelledYN', $input)) {
            if (($input['cancelledYN'] == 0 || $input['cancelledYN'] == -1) && !is_null($input['cancelledYN'])) {
                $purchaseRequests->where('cancelledYN', $input['cancelledYN']);
            }
        }

        if (array_key_exists('PRConfirmedYN', $input)) {
            if (($input['PRConfirmedYN'] == 0 || $input['PRConfirmedYN'] == 1) && !is_null($input['PRConfirmedYN'])) {
                $purchaseRequests->where('PRConfirmedYN', $input['PRConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $purchaseRequests->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseRequests->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseRequests->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        $purchaseRequests = $purchaseRequests->select(
            ['erp_purchaserequest.purchaseRequestID',
                'erp_purchaserequest.purchaseRequestCode',
                'erp_purchaserequest.createdDateTime',
                'erp_purchaserequest.createdUserSystemID',
                'erp_purchaserequest.comments',
                'erp_purchaserequest.location',
                'erp_purchaserequest.priority',
                'erp_purchaserequest.cancelledYN',
                'erp_purchaserequest.PRConfirmedYN',
                'erp_purchaserequest.approved',
                'erp_purchaserequest.timesReferred',
                'erp_purchaserequest.refferedBackYN',
                'erp_purchaserequest.serviceLineSystemID',
                'erp_purchaserequest.financeCategory',
                'erp_purchaserequest.documentSystemID',
                'erp_purchaserequest.manuallyClosed',
                'erp_purchaserequest.prClosedYN',
                'erp_purchaserequest.budgetYear',
                'erp_purchaserequest.isBulkItemJobRun'
            ]);



        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where(function ($query) use ($search) {
                $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        return $purchaseRequests;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['PR Code'] = $val->purchaseRequestCode;
                $data[$x]['Category'] = $val->finance_category? $val->finance_category : '';
                $data[$x]['Segment'] = $val->segment? $val->segment->ServiceLineDes : '';
                $data[$x]['Location'] = $val->location_pdf ? $val->location_pdf->locationName : '';
                $data[$x]['Priority'] = $val->priority_pdf? $val->priority_pdf->priorityDescription : '';
                $data[$x]['Budget Year'] = $val->budgetYear;
                $data[$x]['Comments'] = $val->comments;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::dateFormat($val->createdDateTime);
                $data[$x]['Status'] = StatusService::getStatus($val->cancelledYN, $val->manuallyClosed, $val->PRConfirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
