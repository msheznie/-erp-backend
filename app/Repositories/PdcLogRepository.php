<?php

namespace App\Repositories;

use App\Models\PdcLog;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\Helper;
use App\helper\StatusService;

/**
 * Class PdcLogRepository
 * @package App\Repositories
 * @version September 2, 2021, 2:44 pm +04
 *
 * @method PdcLog findWithoutFail($id, $columns = ['*'])
 * @method PdcLog find($id, $columns = ['*'])
 * @method PdcLog first($columns = ['*'])
*/
class PdcLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentmasterAutoID',
        'paymentBankID',
        'companySystemID',
        'currencyID',
        'chequeRegisterAutoID',
        'chequeNo',
        'chequeDate',
        'chequeStatus',
        'amount',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PdcLog::class;
    }

    public function pdcIssuedListQuery($request, $input, $search = '', $bankmasterAutoID) {
        
        $companyId = $request['companyId'];

        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        if($input['docName'] == "Recevied Cheques") {
            $receivedCheques = PdcLog::where('documentSystemID',21)
            ->whereHas('customer_receive', function ($query){
                $query->where('approved', -1);
            })
            ->when(!empty($input['fromDate']) && !empty($input['toDate']), function ($q) use ($input) {
                $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
                $toDate = Carbon::parse(trim($input['toDate'],'"'));
                return $q->whereBetween('chequeDate', [$fromDate,$toDate]);
            })
            ->when(!empty($input['bank']), function ($q) use ($bankmasterAutoID) {
                return $q->whereIn('paymentBankID', $bankmasterAutoID);
            })
            ->where('companySystemID',$companyId)
            ->with(['currency','bank','customer_receive']);
            return $receivedCheques;
        }else {
            $issuedCheques = PdcLog::where('documentSystemID',4)
            ->whereHas('pay_supplier', function ($query) {
                $query->where('approved', -1);
            })
            ->when(!empty($input['fromDate']) && !empty($input['toDate']), function ($q) use ($input) {
                $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
                $toDate = Carbon::parse(trim($input['toDate'],'"'));
                return $q->whereBetween('chequeDate', [$fromDate,$toDate]);
            })
            ->when(!empty($input['bank']), function ($q) use ($input) {
                return $q->where('paymentBankID', $input['bank']);
            })
            ->where('companySystemID',$companyId)
            ->with(['currency','bank','pay_supplier']);
            return $issuedCheques;
        }

    }


    public function setExportExcelData($dataSet,$input) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.document_code')] = ($input['docName'] != "Recevied Cheques") ? $val->pay_supplier->BPVcode:$val->customer_receive->custPaymentReceiveCode;
                $data[$x][trans('custom.bank_name')] = $val->bank->bankName;
                $data[$x][trans('custom.cheque_no')] = $val->chequeNo;
                $data[$x][trans('custom.cheque_date')] =  Helper::dateFormat($val->chequeDate);
                $data[$x][trans('custom.currency')] = $val->currency->CurrencyCode;
                $data[$x][trans('custom.amount')] = $val->amount;
                $data[$x][trans('custom.status')] = $val->chequeStatusValue;
                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
