<?php
/**
 * =============================================
 * -- File Name : DirectPaymentDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Payment Voucher
 * -- Author : Mohamed Mubashir
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Direct payment detail
 * -- REVISION HISTORY
 * -- Date: 18 September 2018 By: Mubashir Description: Added new function updateDirectPaymentAccount(),deleteAllDirectPayment(),getDirectPaymentDetails()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectPaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateDirectPaymentDetailsAPIRequest;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CurrencyConversion;
use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SegmentMaster;
use App\Repositories\DirectPaymentDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectPaymentDetailsController
 * @package App\Http\Controllers\API
 */
class DirectPaymentDetailsAPIController extends AppBaseController
{
    /** @var  DirectPaymentDetailsRepository */
    private $directPaymentDetailsRepository;

    public function __construct(DirectPaymentDetailsRepository $directPaymentDetailsRepo)
    {
        $this->directPaymentDetailsRepository = $directPaymentDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentDetails",
     *      summary="Get a listing of the DirectPaymentDetails.",
     *      tags={"DirectPaymentDetails"},
     *      description="Get all DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/DirectPaymentDetails")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->directPaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->directPaymentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directPaymentDetails = $this->directPaymentDetailsRepository->all();

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details retrieved successfully');
    }

    /**
     * @param CreateDirectPaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directPaymentDetails",
     *      summary="Store a newly created DirectPaymentDetails in storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Store DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentDetails")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectPaymentDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $payMaster = PaySupplierInvoiceMaster::find($input['directPaymentAutoID']);

        if (empty($payMaster)) {
            return $this->sendError('Payment voucher not found');
        }

        $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return $this->sendError('Selected Bank is not active');
        }

        $bankAccount = BankAccount::isActive()->find($payMaster->BPVAccount);

        if (empty($bankAccount)) {
            return $this->sendError('Selected Bank Account is not active');
        }

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return $this->sendError('Chart of Account not found');
        }

        if ($chartOfAccount->controlAccountsSystemID == 1) {
            return $this->sendError('Cannot add a revenue GL code');
        }

        $company = Company::find($input['companySystemID']);
        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        if ($bankAccount->chartOfAccountSystemID == $input['chartOfAccountSystemID']) {
            return $this->sendError('You are trying to select the same bank account');
        }

        if ($payMaster->expenseClaimOrPettyCash == 6 || $payMaster->expenseClaimOrPettyCash == 7) {

            if(empty($payMaster->interCompanyToSystemID)){
                return $this->sendError('Please select a company to');
            }

            $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['directPaymentAutoID'], 'relatedPartyYN' => 1]);
            if (count($directPaymentDetails) > 0) {
                return $this->sendError('Cannot add GL code as there is a related party GL code added.');
            }

            $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['directPaymentAutoID'], 'relatedPartyYN' => 0]);
            if (count($directPaymentDetails) > 0) {
                if ($chartOfAccount->relatedPartyYN) {
                    return $this->sendError('Cannot add related party GL code as there is a GL code added.');
                }
            }

        }

        $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['directPaymentAutoID'], 'glCodeIsBank' => 1]);
        if (count($directPaymentDetails) > 0) {
            return $this->sendError('Cannot add GL code as there is a bank GL code added.');
        }

        $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['directPaymentAutoID'], 'glCodeIsBank' => 0]);

        if (count($directPaymentDetails) > 0) {
            if ($chartOfAccount->isBank) {
                return $this->sendError('Cannot add bank account GL code as there is a GL code added.');
            }
        }

        $input['companyID'] = $company->CompanyID;

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
            $account = BankAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->where('companySystemID', $input['companySystemID'])->first();
            if($account) {
                $input['bankCurrencyID'] = $account->accountCurrencyID;
                $conversionAmount = \Helper::currencyConversion($input['companySystemID'], $bankAccount->accountCurrencyID, $account->accountCurrencyID, 0);
                $input['bankCurrencyER'] = $conversionAmount["transToDocER"];
            }else{
                return $this->sendError('No bank account found for the selected GL code.');
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

        $directPaymentDetails = $this->directPaymentDetailsRepository->create($input);

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentDetails/{id}",
     *      summary="Display the specified DirectPaymentDetails",
     *      tags={"DirectPaymentDetails"},
     *      description="Get DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectPaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directPaymentDetails/{id}",
     *      summary="Update the specified DirectPaymentDetails in storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Update DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentDetails")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectPaymentDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'chartofaccount']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $payMaster = PaySupplierInvoiceMaster::find($input['directPaymentAutoID']);

        if (empty($payMaster)) {
            return $this->sendError('Direct Payment Supp Master not found');
        }

        $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return $this->sendError('Selected Bank is not active');
        }

        $bankAccount = BankAccount::isActive()->find($payMaster->BPVAccount);

        if (empty($bankAccount)) {
            return $this->sendError('Selected Bank Account is not active');
        }

        if (isset($input['serviceLineSystemID'])) {

            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError('Department not found');
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->directPaymentDetailsRepository->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    return $this->sendError('Please select an active department', 500, $serviceLineError);
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        $conversionAmount = \Helper::convertAmountToLocalRpt(202, $input["directPaymentDetailsID"], ABS($input['DPAmount']));

        $input['localAmount'] = $conversionAmount['localAmount'];
        $input['comRptAmount'] = $conversionAmount['reportingAmount'];
        $input['bankAmount'] = $conversionAmount['defaultAmount'];

        if ($directPaymentDetails->glCodeIsBank) {
            $trasToDefaultER = $input["bankCurrencyER"];
            $bankAmount = 0;
            if ($bankAccount->accountCurrencyID == $directPaymentDetails->bankCurrencyID) {
                $bankAmount = $input['DPAmount'];
            } else {
                if ($trasToDefaultER > $directPaymentDetails->DPAmountCurrencyER) {
                    if ($trasToDefaultER > 1) {
                        $bankAmount = $input['DPAmount'] / $trasToDefaultER;
                    } else {
                        $bankAmount = $input['DPAmount'] * $trasToDefaultER;
                    }
                } else {
                    If ($trasToDefaultER > 1) {
                        $bankAmount = $input['DPAmount'] * $trasToDefaultER;
                    } else {
                        $bankAmount = $input['DPAmount'] / $trasToDefaultER;
                    }
                }
            }

            if ($directPaymentDetails->bankCurrencyID == $directPaymentDetails->localCurrency) {
                $input['localAmount'] = \Helper::roundValue($bankAmount);
                $input['localCurrencyER'] = $input["bankCurrencyER"];
            }else{
                $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->bankCurrencyID)->where('subCurrencyID', $directPaymentDetails->localCurrency)->first();
                if ($conversion->conversion > 1) {
                    if ($conversion->conversion > 1) {
                        $input['localAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    } else {
                        $input['localAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    }
                } else {
                    if ($conversion->conversion > 1) {
                        $input['localAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    } else {
                        $input['localAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    }
                }
            }

            if ($directPaymentDetails->bankCurrencyID == $directPaymentDetails->comRptCurrency) {
                $input['comRptAmount'] = \Helper::roundValue($bankAmount);
                $input['comRptCurrencyER'] = $input["bankCurrencyER"];
            }else{
                $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->bankCurrencyID)->where('subCurrencyID', $directPaymentDetails->comRptCurrency)->first();
                if ($conversion->conversion > 1) {
                    if ($conversion->conversion > 1) {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    } else {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    }
                } else {
                    if ($conversion->conversion > 1) {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    } else {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    }
                }
            }

            $input['bankAmount'] = \Helper::roundValue($bankAmount);
        }

        $directPaymentDetails = $this->directPaymentDetailsRepository->update($input, $id);

        return $this->sendResponse($directPaymentDetails->toArray(), 'DirectPaymentDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directPaymentDetails/{id}",
     *      summary="Remove the specified DirectPaymentDetails from storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Delete DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $directPaymentDetails->delete();

        return $this->sendResponse($id, 'Direct Payment Details deleted successfully');
    }


    public function getDirectPaymentDetails(Request $request)
    {
        $id = $request->PayMasterAutoId;

        $directPaymentDetails = $this->directPaymentDetailsRepository->with(['segment', 'chartofaccount'])->findWhere(['directPaymentAutoID' => $id]);

        return $this->sendResponse($directPaymentDetails, 'Details retrieved successfully');
    }

    public function deleteAllDirectPayment(Request $request)
    {

        $id = $request->directPaymentAutoID;

        $directPaymentDetails = DirectPaymentDetails::where('directPaymentAutoID', $id)->delete();

        return $this->sendResponse($directPaymentDetails, 'Successfully delete');
    }

    public function updateDirectPaymentAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = [
            'not_in' => 'The :attribute field is required.',
        ];

        $validator = \Validator::make($input, [
            'toBankID' => 'required|not_in:0',
            'toBankAccountID' => 'required|not_in:0',
        ], $messages);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
        }

        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($input['directPaymentDetailsID']);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['toBankCurrencyID'], $input['toBankCurrencyID'], $input['toBankAmount']);

        $company = Company::find($input['companySystemID']);
        $bankAccount = BankAccount::find($input['toBankAccountID']);
        $chartofaccount = ChartOfAccount::find($bankAccount->chartOfAccountSystemID);

        $input['toCompanyLocalCurrencyID'] = $company->localCurrencyID;
        $input['toCompanyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        $input['toCompanyLocalCurrencyAmount'] = $companyCurrencyConversion['localAmount'];
        $input['toCompanyRptCurrencyID'] = $company->reportingCurrency;
        $input['toCompanyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        $input['toCompanyRptCurrencyAmount'] = $companyCurrencyConversion['reportingAmount'];
        $input['toBankGlCodeSystemID'] = $bankAccount->chartOfAccountSystemID;
        $input['toBankGlCode'] = $chartofaccount->AccountCode;
        $input['toBankGLDescription'] = $chartofaccount->AccountDescription;
        unset($input['companySystemID']);

        $directPaymentDetails = $this->directPaymentDetailsRepository->update($input, $input['directPaymentDetailsID']);

        return $this->sendResponse($directPaymentDetails->toArray(), 'DirectPaymentDetails updated successfully');

    }

    public function getDPExchangeRateAmount(Request $request)
    {
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($request->directPaymentDetailsID);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        if ($request->toBankCurrencyID) {

            $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->bankCurrencyID)->where('subCurrencyID', $request->toBankCurrencyID)->first();
            $conversion = $conversion->conversion;

            $bankAmount = 0;
            if ($request->toBankCurrencyID == $directPaymentDetails->bankCurrencyID) {
                $bankAmount = $directPaymentDetails->DPAmount;
            } else {
                if ($conversion > $directPaymentDetails->DPAmountCurrencyER) {
                    if ($conversion > 1) {
                        $bankAmount = $directPaymentDetails->DPAmount / $conversion;
                    } else {
                        $bankAmount = $directPaymentDetails->DPAmount * $conversion;
                    }
                } else {
                    If ($conversion > 1) {
                        $bankAmount = $directPaymentDetails->DPAmount * $conversion;
                    } else {
                        $bankAmount = $directPaymentDetails->DPAmount / $conversion;
                    }
                }
            }

            $output = ['toBankCurrencyER' => $conversion, 'toBankAmount' => $bankAmount];
            return $this->sendResponse($output, 'Successfully data retrieved');
        } else {
            $output = ['toBankCurrencyER' => 0, 'toBankAmount' => 0];
            return $this->sendResponse($output, 'Successfully data retrieved');
        }
    }

}
