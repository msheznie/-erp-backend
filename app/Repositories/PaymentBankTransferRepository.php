<?php

namespace App\Repositories;

use App\Models\PaymentBankTransfer;
use InfyOm\Generator\Common\BaseRepository;
use Carbon\Carbon;
use App\helper\StatusService;

/**
 * Class PaymentBankTransferRepository
 * @package App\Repositories
 * @version October 2, 2018, 10:55 am UTC
 *
 * @method PaymentBankTransfer findWithoutFail($id, $columns = ['*'])
 * @method PaymentBankTransfer find($id, $columns = ['*'])
 * @method PaymentBankTransfer first($columns = ['*'])
*/
class PaymentBankTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'bankTransferDocumentCode',
        'serialNumber',
        'documentDate',
        'bankMasterID',
        'bankAccountAutoID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'RollLevForApp_curr',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentBankTransfer::class;
    }

    public function paymentBankTransferListQuery($request, $input, $search = '', $bankmasterAutoID, $approved = 0) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankTransfer = PaymentBankTransfer::whereIn('companySystemID', $subCompanies)
                                           ->with(['created_by', 'bank_account' => function ($query) {
                                               $query->with(['bank' => function($q) {
                                                   $q->with(['config']);
                                               }]);
                                           }]);

        if (isset($input['month']) && $input['month'] != null) {
            $month = Carbon::parse($input['month'])->format('m');

            $bankTransfer = $bankTransfer->whereMonth('documentDate', $month);
        }

        if (isset($input['forReview']) && $input['forReview']) {
            $bankTransfer = $bankTransfer->where('confirmedYN', 1);
        }

        if (isset($input['year']) && $input['year'] != null) {
            $year = Carbon::parse($input['year'])->format('Y');

            $bankTransfer = $bankTransfer->whereYear('documentDate', $year);
        }

        if (isset($input['bankAccountAutoID']) && $input['bankAccountAutoID'] > 0) {
            $bankTransfer = $bankTransfer->where('bankAccountAutoID', $input['bankAccountAutoID']);
        }

        if (isset($input['bankmasterAutoID']) && $input['bankmasterAutoID'] > 0) {
            $bankTransfer = $bankTransfer->whereHas('bank_account', function($query) use ($bankmasterAutoID) {
                                                            $query->whereIn('bankmasterAutoID', $bankmasterAutoID);
                                                    });
        }

        if($approved == 1) {
            $bankTransfer = $bankTransfer->where('approvedYN', -1);
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankTransfer = $bankTransfer->where(function ($query) use ($search) {
                $query->where('bankTransferDocumentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhereHas('ledger_data', function ($query) use ($search) {
                        $query->where('documentCode', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $bankTransfer;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.document_code')] = $val->bankTransferDocumentCode;
                $data[$x][trans('custom.document_date')] = \Helper::dateFormat($val->documentDate);
                $data[$x][trans('custom.bank_name')] = $val->bank_account? $val->bank_account->bankName : '';
                $data[$x][trans('custom.account_no')] = $val->bank_account? $val->bank_account->AccountNo : '';
                $data[$x][trans('custom.narration')] = $val->narration;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->canceledYN, NULL, $val->confirmedYN, $val->approvedYN, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
