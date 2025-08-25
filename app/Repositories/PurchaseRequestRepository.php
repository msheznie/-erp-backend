<?php

namespace App\Repositories;

use App\Models\BudgetMaster;
use App\Models\CompanyFinanceYear;
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
    protected $data = [];
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

    public function purchaseRequestListQuery($request, $input, $search = '', $serviceLineSystemID, $buyerEmpSystemId) {

        $purchaseRequests = PurchaseRequest::where('companySystemID', $input['companyId']);


        if (array_key_exists('requestReview', $input)) {
            if ($input['requestReview'] == 1) {
                $purchaseRequests->where('cancelledYN', 0);
                //->where('approved', -1);
            }
        } else {
            $purchaseRequests = $purchaseRequests->where('documentSystemID', $input['documentId']);
        }


        $purchaseRequests = $purchaseRequests->with(['details' => function($query){
            $query->with(['uom']);
        },'created_by' => function ($query) {
        }, 'priority' => function ($query) {

        }, 'location' => function ($query) {

        }, 'segment' => function ($query) {

        }, 'financeCategory' => function ($query) {

        }, 'location_pdf','priority_pdf','currency_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseRequests->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('buyerEmpSystemID', $input)) {
            if ($input['buyerEmpSystemID'] && !is_null($input['buyerEmpSystemID'])) {
                $purchaseRequests->whereIn('buyerEmpSystemID', $buyerEmpSystemId);
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

        
        if (isset($request['fromPortal']) && $request['fromPortal']) {
            $purchaseRequests = $purchaseRequests->where('createdUserSystemID', $request['createdUserSystemID']);
            $purchaseRequests = $purchaseRequests->whereNotIn('purchaseRequestID', function($query) {
                    $query->select('purcahseRequestID')->from('erp_pulled_from_mr');
                });
        }
        
        $purchaseRequests = $purchaseRequests->select(
            ['erp_purchaserequest.purchaseRequestID',
                'erp_purchaserequest.purchaseRequestCode',
                'erp_purchaserequest.createdDateTime',
                'erp_purchaserequest.createdUserSystemID',
                'erp_purchaserequest.comments',
                'erp_purchaserequest.internalNotes',
                'erp_purchaserequest.location',
                'erp_purchaserequest.priority',
                'erp_purchaserequest.cancelledYN',
                'erp_purchaserequest.PRConfirmedYN',
                'erp_purchaserequest.approved',
                'erp_purchaserequest.timesReferred',
                'erp_purchaserequest.refferedBackYN',
                'erp_purchaserequest.serviceLineSystemID',
                'erp_purchaserequest.buyerEmpName',
                'erp_purchaserequest.financeCategory',
                'erp_purchaserequest.documentSystemID',
                'erp_purchaserequest.manuallyClosed',
                'erp_purchaserequest.prClosedYN',
                'erp_purchaserequest.budgetYear',
                'erp_purchaserequest.isBulkItemJobRun',
                'erp_purchaserequest.currency',
                'erp_purchaserequest.buyerEmpSystemID'
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
                $data[$x]['Buyer'] = ($val->buyerEmpSystemID !== null && $val->buyerEmpSystemID != 0) ? $val->buyerEmpName : '';
                $data[$x]['Budget Year'] = $val->budgetYear;
                $data[$x]['Comments'] = $val->comments;
                $data[$x]['Internal Note'] = $val->internalNotes;
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

    public function setExportExcelDataDetail($dataSet) {

        $this->data = [];
        $dataSet = $dataSet->get();
        foreach ($dataSet as $val) {
            $currency = $val->currency_by ? $val->currency_by->CurrencyCode : '';
            $this->mainHeader();
            $this->headerDetails($val);
            $this->data[] = [];
            $this->detailDetails($val->details,$currency);
            $this->data[] = [];

        }
        
        return $this->data;
    }

    private function mainHeader() {
        $this->data[] = [
            'IsHeader' => true,
            'PR Code' => 'PR Code',
            'Category' => 'Category',
            'Segment' => 'Segment',
            'Location' => 'Location',
            'Priority' => 'Priority',
            'Buyer' => 'Buyer',
            'Budget Year' => 'Budget Year',
            'Comments' => 'Comments',
            'Internal Note' => 'Internal Note',
            'Created By' => 'Created By',
            'Created At' => 'Created At',
            'Status' => 'Status'
        ];
    }

    private function headerDetails($val) {
        $this->data[] = [
            'IsHeader' => false,
            'PR Code' => $val->purchaseRequestCode,
            'Category' => $val->finance_category ? $val->finance_category : '',
            'Segment' => $val->segment ? $val->segment->ServiceLineDes : '',
            'Location' => $val->location_pdf ? $val->location_pdf->locationName : '',
            'Priority' => $val->priority_pdf ? $val->priority_pdf->priorityDescription : '',
            'Buyer' => ($val->buyerEmpSystemID !== null && $val->buyerEmpSystemID != 0) ? $val->buyerEmpName : '',
            'Budget Year' => $val->budgetYear,
            'Comments' => $val->comments,
            'Internal Note' => $val->internalNotes,
            'Created By' => $val->created_by? $val->created_by->empName : '',
            'Created At' =>\Helper::dateFormat($val->createdDateTime),
            'Status' => StatusService::getStatus($val->cancelledYN, $val->manuallyClosed, $val->PRConfirmedYN, $val->approved, $val->refferedBackYN)
        ];
    }

    private function detailDetails($val,$currency) {
         if (!empty($val) && count($val) > 0) {
            $headerOne ['Details'] = 'Details';
            $headerOne ['IsHeader'] = true;

            $this->data[] = $headerOne;
            $header = [];
            $header ['IsHeader'] = true;
            $header['item Code'] = 'Item Code';
            $header['Description'] = 'Item Description';
            $header['UOM'] = 'UOM';
            $header['Qty Requested'] = 'Qty Requested';
            $header['Estimated Unit Cost'] = 'Estimated Unit Cost (' . $currency . ')';
            $header['Total'] = 'Total';
            $header['Qty On Order'] = 'Qty On Order';
            $header['Qty in Hand'] = 'Qty in Hand';
            $this->data[] = $header;

            $totalQtyRequested = 0;
            $totalCost = 0;

            foreach ($val as $detail) {
                $row = [];
                $row['IsHeader'] =false;
                $row['item Code'] = $detail->itemPrimaryCode ?? '';
                $row['Description'] = $detail->itemDescription ?? '';
                $row['UOM'] = $detail->uom->UnitDes ?? '';
                $row['Qty Requested'] = $detail->quantityRequested ?? '';
                $row['Estimated Unit Cost'] = $detail->estimatedCost ?? '';
                $row['Total'] = $detail->totalCost ?? '';
                $row['Qty On Order'] = $detail->quantityOnOrder ?? '';
                $row['Qty in Hand'] = $detail->quantityInHand ?? '';
                $this->data[] = $row;
                $totalQtyRequested += $detail->quantityRequested;
                $totalCost += $detail->estimatedCost;
            }
            $totalRow = [
                'IsHeader' => true,
                'item Code' => '', 
                'Description' => '',
                'UOM' => 'Total',
                'Qty Requested' => $totalQtyRequested,
                'Estimated Unit Cost' => $totalCost,
                'Total' => '',
                'Qty On Order' => '',
                'Qty in Hand' => ''
            ];
        $this->data[] = $totalRow;
            $this->data[] = [];
         }
    }
    public function notifyPRFinancialYear($companySystemID)
    {
        try{
            $currentFinancialYear = CompanyFinanceYear::currentFinanceYear($companySystemID);
            if(empty($currentFinancialYear)){
                return [
                    'success' => true,
                    'message' => trans('custom.no_active_financial_year'),
                    'data' => ['notifyYN' => false]
                ];
            }
            $budgetMaster = BudgetMaster::getBudgetMasterByFYear($currentFinancialYear->companyFinanceYearID, $companySystemID);
            if(empty($budgetMaster)){
                return [
                    'success' => true,
                    'message' => trans('custom.no_budget_found'),
                    'data' => ['notifyYN' => false]
                ];
            }
            $yearStartDate = $currentFinancialYear->startDate;
            $yearEndDate = $currentFinancialYear->endDate;
            $year = (int) date('Y', strtotime($yearStartDate));
            $message = "<span>" . trans('custom.current_active_period', [
                    'start' => $yearStartDate,
                    'end' => $yearEndDate
                ]) . "</span>
            <br>" . trans('custom.selected_budget_year', ['year' => $year]) . "<br><br>
            <span>" . trans('custom.proceed_question') . "</span>";

            return [
                'success' => true,
                'message' => $message,
                'data' => ['notifyYN' => true]
            ];

        } catch (\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
}
