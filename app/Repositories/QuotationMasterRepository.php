<?php

namespace App\Repositories;

use App\Models\QuotationMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class QuotationMasterRepository
 * @package App\Repositories
 * @version January 22, 2019, 1:56 pm +04
 *
 * @method QuotationMaster findWithoutFail($id, $columns = ['*'])
 * @method QuotationMaster find($id, $columns = ['*'])
 * @method QuotationMaster first($columns = ['*'])
*/
class QuotationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'documentDate',
        'documentExpDate',
        'salesPersonID',
        'versionNo',
        'referenceNo',
        'narration',
        'Note',
        'contactPersonName',
        'contactPersonNumber',
        'customerSystemCode',
        'customerCode',
        'customerName',
        'customerAddress',
        'customerTelephone',
        'customerFax',
        'customerEmail',
        'customerReceivableAutoID',
        'customerReceivableSystemGLCode',
        'customerReceivableGLAccount',
        'customerReceivableDescription',
        'customerReceivableType',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalAmount',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyExchangeRate',
        'customerCurrencyAmount',
        'customerCurrencyDecimalPlaces',
        'isDeleted',
        'deletedEmpID',
        'deletedDate',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedEmpSystemID',
        'approvedbyEmpID',
        'approvedbyEmpName',
        'RollLevForApp_curr',
        'closedYN',
        'closedDate',
        'closedReason',
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
        'timestamp',
        'leadTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationMaster::class;
    }

    public function quotationMasterListQuery($request, $input, $search = '') {

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $quotationMaster = QuotationMaster::whereIn('companySystemID', $childCompanies)
            ->where('documentSystemID', $input['documentSystemID'])
        ->with(['segment','local_currency','reporting_currency','transaction_currency']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $quotationMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $quotationMaster->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month']) && $input['month'] != [0]) {
                $quotationMaster->whereMonth('documentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year']) && $input['year'] != [0]) {
                $quotationMaster->whereYear('documentDate', '=', $input['year']);
            }
        }

        if (array_key_exists('customerSystemCode', $input)) {
            if ($input['customerSystemCode'] && !is_null($input['customerSystemCode'])) {
                $customerSystemCode = $request['customerSystemCode'];
                $customerSystemCode = (array)$customerSystemCode;
                $customerSystemCode = collect($customerSystemCode)->pluck('id');
                $quotationMaster->whereIn('customerSystemCode', $customerSystemCode);
            }
        }

        if (array_key_exists('salesPersonID', $input)) {
            if ($input['salesPersonID'] && !is_null($input['salesPersonID'])) {
                $salesPersonID = $request['salesPersonID'];
                $salesPersonID= (array)$salesPersonID;
                $salesPersonID = collect($salesPersonID)->pluck('id');
                $quotationMaster->whereIn('salesPersonID', $salesPersonID);
            }
        }


            if (array_key_exists('quotationType', $input)) {
                if ($input['quotationType'] && !is_null($input['quotationType']) && $input['quotationType'] != [0]) {
                    $quotationMaster->where('quotationType', $input['quotationType']);
                }
            }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $quotationMaster = $quotationMaster->where(function ($query) use ($search) {
                $query->where('quotationCode', 'LIKE', "%{$search}%");
            });
        }

        $data['search']['value'] = '';
        $request->merge($data);

        $request->request->remove('search.value');

        return $quotationMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Document Code'] = $val->quotationCode;
                $data[$x]['Type'] = StatusService::getQuotationType($val->quotationType, $val->documentSystemID);
                $data[$x]['Customer Name'] = $val->customerName;
                $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                $data[$x]['Document Exp Date'] = \Helper::dateFormat($val->documentExpDate);
                $data[$x]['Segment'] = $val->segment? $val->segment->ServiceLineDes : '';
                $data[$x]['Comments'] = $val->narration;
                $data[$x]['Created By'] = $val->createdUserName;
                $data[$x]['Created At'] = \Helper::dateFormat($val->createdDateTime);
                $data[$x]['Confirmed on'] = \Helper::dateFormat($val->confirmedDate);
                $data[$x]['Approved on'] = \Helper::dateFormat($val->approvedDate);
                $data[$x]['Transaction Currency'] = $val->transaction_currency? $val->transaction_currency->CurrencyCode : '';
                $data[$x]['Transaction Amount'] = number_format($val->transactionAmount, $val->transaction_currency? $val->transaction_currency->DecimalPlaces : '', ".", "");
                
                $data[$x]['Local Currency'] = $val->local_currency? $val->local_currency->CurrencyCode : '';
                $data[$x]['Local Amount'] = number_format($val->companyLocalAmount, $val->local_currency? $val->local_currency->DecimalPlaces : '', ".", "");
                $data[$x]['Reporting Currency'] = $val->reporting_currency? $val->reporting_currency->CurrencyCode : '';
                $data[$x]['Reporting Amount'] = number_format($val->companyReportingAmount, $val->reporting_currency? $val->reporting_currency->DecimalPlaces : '', ".", "");

                $data[$x]['Status'] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approvedYN, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
