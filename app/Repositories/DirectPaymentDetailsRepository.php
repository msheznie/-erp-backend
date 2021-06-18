<?php

namespace App\Repositories;

use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\BankAssign;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Company;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DirectPaymentDetailsRepository
 * @package App\Repositories
 * @version August 9, 2018, 9:59 am UTC
 *
 * @method DirectPaymentDetails findWithoutFail($id, $columns = ['*'])
 * @method DirectPaymentDetails find($id, $columns = ['*'])
 * @method DirectPaymentDetails first($columns = ['*'])
*/
class DirectPaymentDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directPaymentAutoID',
        'companyID',
        'serviceLineCode',
        'supplierID',
        'expenseClaimMasterAutoID',
        'glCode',
        'glCodeDes',
        'glCodeIsBank',
        'comments',
        'supplierTransCurrencyID',
        'supplierTransER',
        'DPAmountCurrency',
        'DPAmountCurrencyER',
        'DPAmount',
        'bankAmount',
        'bankCurrencyID',
        'bankCurrencyER',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'timesReferred',
        'relatedPartyYN',
        'pettyCashYN',
        'glCompanySystemID',
        'glCompanyID',
        'toBankID',
        'toBankAccountID',
        'toBankCurrencyID',
        'toBankCurrencyER',
        'toBankAmount',
        'toBankGlCode',
        'toBankGLDescription',
        'toCompanyLocalCurrencyID',
        'toCompanyLocalCurrencyER',
        'toCompanyLocalCurrencyAmount',
        'toCompanyRptCurrencyID',
        'toCompanyRptCurrencyER',
        'toCompanyRptCurrencyAmount',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DirectPaymentDetails::class;
    }

    public function storeDirectDetail($directPaymentAutoID, $chartOfAccountSystemID)
    {
        $chartOfAccount = ChartOfAccount::find($chartOfAccountSystemID);
        if (empty($chartOfAccount)) {
            return ['status' => false, 'message' => 'Chart of Account not found'];
        }

        if ($chartOfAccount->controlAccountsSystemID == 1) {
            return ['status' => false, 'message' => 'Cannot add a revenue GL code - '.$chartOfAccount->AccountCode];
        }

        $payMaster = PaySupplierInvoiceMaster::find($directPaymentAutoID);

        $bankAccount = BankAccount::isActive()->find($payMaster->BPVAccount);

        if ($bankAccount->chartOfAccountSystemID == $chartOfAccountSystemID) {
            return ['status' => false, 'message' => 'Cannot add , GL code - '.$chartOfAccount->AccountCode.' is same as bank account'];
        }


        $company = Company::find($payMaster->companySystemID);

        // if ($payMaster->expenseClaimOrPettyCash == 6 || $payMaster->expenseClaimOrPettyCash == 7) {
        //     $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['directPaymentAutoID'], 'relatedPartyYN' => 1]);
        //     if (count($directPaymentDetails) > 0) {
        //         return $this->sendError('Cannot add GL code as there is a related party GL code added.');
        //     }
        // }

        
        // $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['directPaymentAutoID'], 'glCodeIsBank' => 0]);

        // if (count($directPaymentDetails) > 0) {
        //     if ($chartOfAccount->isBank) {
        //         return $this->sendError('Cannot add bank account GL code as there is a GL code added.');
        //         return ['status' => false, 'message' => 'GL code - '.$chartOfAccount->AccountCode.' cannot be added, Becuase already bank gl codes are added'];
        //     }
        // }

        $input['companyID'] = $company->CompanyID;

        $input['DPAmount'] = 0;
        $input['bankCurrencyER'] = 0;
        $input['chartOfAccountSystemID'] = $chartOfAccountSystemID;
        $input['comments'] = '';
        $input['companySystemID'] = $payMaster->companySystemID;
        $input['directPaymentAutoID'] = $directPaymentAutoID;
        $input['serviceLineSystemID'] = null;


        $input['glCode'] = $chartOfAccount->AccountCode;
        $input['glCodeDes'] = $chartOfAccount->AccountDescription;
        $input['glCodeIsBank'] = $chartOfAccount->isBank;
        $input['relatedPartyYN'] = $chartOfAccount->relatedPartyYN;

        $input['supplierTransCurrencyID'] = $payMaster->supplierTransCurrencyID;
        $input['supplierTransER'] = 1;
        $input['DPAmountCurrency'] = $payMaster->supplierTransCurrencyID;
        $input['DPAmountCurrencyER'] = 1;
        $input['localCurrency'] = $payMaster->localCurrencyID;
        $input['localCurrencyER'] = $payMaster->localCurrencyER;
        $input['comRptCurrency'] = $payMaster->companyRptCurrencyID;
        $input['comRptCurrencyER'] = $payMaster->companyRptCurrencyER;

        if ($chartOfAccount->isBank) {
            $account = BankAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->where('companySystemID', $payMaster->companySystemID)->first();
            if($account) {
                $input['bankCurrencyID'] = $account->accountCurrencyID;
                $conversionAmount = \Helper::currencyConversion($payMaster->companySystemID, $bankAccount->accountCurrencyID, $account->accountCurrencyID, 0);
                $input['bankCurrencyER'] = $conversionAmount["transToDocER"];
            }else{
                return ['status' => false, 'message' => 'GL code - '.$chartOfAccount->AccountCode.' cannot be added, No bank accound found.'];
            }
        } else {
            $input['bankCurrencyID'] = $payMaster->BPVbankCurrency;
            $input['bankCurrencyER'] = $payMaster->BPVbankCurrencyER;
        }

        if ($payMaster->BPVsupplierID) {
            $input['supplierTransCurrencyID'] = $payMaster->supplierTransCurrencyID;
            $input['supplierTransER'] = $payMaster->supplierTransCurrencyER;
        }

        if ($payMaster->FYBiggin) {
            $finYearExp = explode('-', $payMaster->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = date("Y");
        }

        $directPaymentDetails = $this->model->create($input);

        return ['status' => true, 'message' => 'Direct Payment Details saved successfully'];
    }
}
