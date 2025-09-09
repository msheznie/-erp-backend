<?php

namespace App\Repositories;

use App\Models\MatchDocumentMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class MatchDocumentMasterRepository
 * @package App\Repositories
 * @version September 11, 2018, 10:20 am UTC
 *
 * @method MatchDocumentMaster findWithoutFail($id, $columns = ['*'])
 * @method MatchDocumentMaster find($id, $columns = ['*'])
 * @method MatchDocumentMaster first($columns = ['*'])
*/
class MatchDocumentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'PayMasterAutoId',
        'documentSystemID',
        'companyID',
        'companySystemID',
        'documentID',
        'serialNo',
        'matchingDocCode',
        'matchingDocdate',
        'BPVcode',
        'BPVdate',
        'BPVNarration',
        'directPaymentPayee',
        'directPayeeCurrency',
        'BPVsupplierID',
        'supplierGLCode',
        'supplierTransCurrencyID',
        'supplierTransCurrencyER',
        'supplierDefCurrencyID',
        'supplierDefCurrencyER',
        'localCurrencyID',
        'localCurrencyER',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'payAmountBank',
        'payAmountSuppTrans',
        'payAmountSuppDef',
        'suppAmountDocTotal',
        'payAmountCompLocal',
        'payAmountCompRpt',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByEmpSystemID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'invoiceType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'matchingAmount',
        'matchBalanceAmount',
        'matchedAmount',
        'matchLocalAmount',
        'matchRptAmount',
        'matchingType',
        'matchingOption',
        'isExchangematch',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MatchDocumentMaster::class;
    }

    public function matchDocumentListQuery($request, $input, $search = '', $supplierID) {

        $invMaster = MatchDocumentMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->whereIn('documentSystemID', [4, 15]);
        $invMaster->with(['created_by' => function ($query) {
        }, 'supplier' => function ($query) {
        }, 'employee' => function ($query) {
        }, 'transactioncurrency' => function ($query) {
        }, 'localcurrency' => function ($query) {
        }, 'rptcurrency' => function ($query) {
        },'cancelled_by']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('matchingConfirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('matchingDocdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('matchingDocdate', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $invMaster->whereIn('BPVsupplierID', $supplierID);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('matchingDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")
                    ->orWhere('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                            ->orWhere('supplierName', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $invMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][__('custom.matching_code')] = $val->matchingDocCode;
                $data[$x][__('custom.matching_date')] = \Helper::convertDateWithTime($val->matchingDocdate);
                $data[$x][__('custom.document_code')] = $val->BPVcode;
                $data[$x][__('custom.e_supplier_code')] = $val->primarySupplierCode;
                $data[$x][__('custom.supplier_name')] = $val->supplier? $val->supplier->supplierName : '';
                $data[$x][__('custom.comments')] = $val->BPVNarration;
                $data[$x][__('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][__('custom.cancelled_by')] = $val->cancelled_by? $val->cancelled_by->empName : '';
                $data[$x][__('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][__('custom.cancelled_at')] = \Helper::convertDateWithTime($val->cancelledDate);
                $data[$x][__('custom.confirmed_on')] = \Helper::convertDateWithTime($val->confirmedDate);
                
                $data[$x][__('custom.e_transaction_currency')] = $val->supplierTransCurrencyID? ($val->transactioncurrency? $val->transactioncurrency->CurrencyCode : '') : '';
                $data[$x][__('custom.e_transaction_amount')] = $val->transactioncurrency? number_format($val->payAmountSuppTrans,  $val->transactioncurrency->DecimalPlaces, ".", "") : '';
                $data[$x][__('custom.local_currency')] = $val->localCurrencyID? ($val->localcurrency? $val->localcurrency->CurrencyCode : '') : '';
                $data[$x][__('custom.local_amount')] = $val->localcurrency? number_format($val->payAmountCompLocal,  $val->localcurrency->DecimalPlaces, ".", "") : '';
                $data[$x][__('custom.reporting_currency')] = $val->companyRptCurrencyID? ($val->rptcurrency? $val->rptcurrency->CurrencyCode : '') : '';
                $data[$x][__('custom.reporting_amount')] = $val->rptcurrency? number_format($val->payAmountCompRpt,  $val->rptcurrency->DecimalPlaces, ".", "") : '';

                if($val->matchingConfirmedYN == 0 && $val->cancelledYN == 0){
                    $data[$x][__('custom.e_status')] = "Not Confirmed";
                } else if ($val->matchingConfirmedYN == 1 && $val->cancelledYN == 0) {
                    $data[$x][__('custom.e_status')] = "Confirmed";
                } else if ($val->cancelledYN == 1) {
                    $data[$x][__('custom.e_status')] = "Cancelled";
                }
                // $data[$x]['Status'] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

    public function receiptVoucherMatchingListQuery($request, $input, $search = '', $customerID) {

        $invMaster = MatchDocumentMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->whereIn('documentSystemID', [19, 21]);
        $invMaster->with(['segment','documentSystemMapping','created_by' => function ($query) {
        }, 'customer' => function ($query) {
        }, 'transactioncurrency' => function ($query) {
        }, 'localcurrency' => function ($query) {
        }, 'rptcurrency' => function ($query) {
        }, 'cancelled_by']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('matchingConfirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('matchingDocdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('matchingDocdate', '=', $input['year']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if ($input['customerID'] && !is_null($input['customerID'])) {
                $invMaster->whereIn('BPVsupplierID', $customerID);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $invMaster = $invMaster->where(function ($query) use ($search, $search_without_comma) {
                $query->where('matchingDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")
                    ->orWhere('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('payAmountSuppTrans', 'LIKE', "%{$search}%")
                    ->orWhere('matchingAmount', 'LIKE', "%{$search_without_comma}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('CutomerCode', 'LIKE', "%{$search}%")
                            ->orWhere('CustomerName', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $invMaster;
    }

    public function setReceiptVoucherMatchingExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Matching Code'] = $val->matchingDocCode;
                $data[$x]['Matching Date'] = \Helper::dateFormat($val->matchingDocdate);
                $data[$x]['Document Code'] = $val->BPVcode;
                $data[$x]['Customer Code'] = $val->customer? $val->customer->CutomerCode : '';
                $data[$x]['Customer Name'] = $val->customer? $val->customer->CustomerName : '';
                $data[$x]['Comments'] = $val->BPVNarration;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Cancelled By'] = $val->cancelled_by? $val->cancelled_by->empName : '';
                $data[$x]['Created At'] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x]['Cancelled At'] = \Helper::convertDateWithTime($val->cancelledDate);
                $data[$x]['Confirmed on'] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x]['Currency'] = $val->transactioncurrency?  $val->transactioncurrency->CurrencyCode : '';
                $data[$x]['Receipt Amount'] = number_format($val->payAmountSuppTrans, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "");
                $data[$x]['Matched Amount'] = number_format($val->matchedAmount, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "");

               
                $data[$x]['Local Currency'] = $val->localCurrencyID? ($val->localcurrency? $val->localcurrency->CurrencyCode : '') : '';
                $data[$x]['Local Amount'] = $val->localcurrency? number_format($val->payAmountCompLocal,  $val->localcurrency->DecimalPlaces, ".", "") : '';
                $data[$x]['Reporting Currency'] = $val->companyRptCurrencyID? ($val->rptcurrency? $val->rptcurrency->CurrencyCode : '') : '';
                $data[$x]['Reporting Amount'] = $val->rptcurrency? number_format($val->payAmountCompRpt,  $val->rptcurrency->DecimalPlaces, ".", "") : '';

                if($val->matchingConfirmedYN == 0 && $val->cancelledYN == 0){
                    $data[$x]['Status'] = "Not Confirmed";
                } else if ($val->matchingConfirmedYN == 1 && $val->cancelledYN == 0) {
                    $data[$x]['Status'] = "Confirmed";
                } else if ($val->cancelledYN == 1) {
                    $data[$x]['Status'] = "Cancelled";
                }
                // $data[$x]['Status'] = StatusService::getStatus( $val->cancelledYN, NULL, $val->matchingConfirmedYN, NULL, NULL);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
;
