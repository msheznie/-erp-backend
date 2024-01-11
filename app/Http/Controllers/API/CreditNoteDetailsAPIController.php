<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteDetailsAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNoteDetails;
use App\helper\TaxService;
use App\Models\CreditNote;
use App\Models\ChartOfAccount;
use App\Models\Contract;
use App\Models\SegmentMaster;
use App\Repositories\CreditNoteDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CreditNoteDetailsController
 * @package App\Http\Controllers\API
 */
class CreditNoteDetailsAPIController extends AppBaseController
{
    /** @var  CreditNoteDetailsRepository */
    private $creditNoteDetailsRepository;

    public function __construct(CreditNoteDetailsRepository $creditNoteDetailsRepo)
    {
        $this->creditNoteDetailsRepository = $creditNoteDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteDetails",
     *      summary="Get a listing of the CreditNoteDetails.",
     *      tags={"CreditNoteDetails"},
     *      description="Get all CreditNoteDetails",
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
     *                  @SWG\Items(ref="#/definitions/CreditNoteDetails")
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
        $this->creditNoteDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNoteDetails = $this->creditNoteDetailsRepository->all();

        return $this->sendResponse($creditNoteDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.credit_note_details')]));
    }

    /**
     * @param CreateCreditNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNoteDetails",
     *      summary="Store a newly created CreditNoteDetails in storage",
     *      tags={"CreditNoteDetails"},
     *      description="Store CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteDetails")
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
     *                  ref="#/definitions/CreditNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteDetailsAPIRequest $request)
    {
        $input = $request->all();

        $creditNoteDetails = $this->creditNoteDetailsRepository->create($input);

        return $this->sendResponse($creditNoteDetails->toArray(), trans('custom.save', ['attribute' => trans('custom.credit_note_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteDetails/{id}",
     *      summary="Display the specified CreditNoteDetails",
     *      tags={"CreditNoteDetails"},
     *      description="Get CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetails",
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
     *                  ref="#/definitions/CreditNoteDetails"
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
        /** @var CreditNoteDetails $creditNoteDetails */
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.credit_note_details')]));
        }

        return $this->sendResponse($creditNoteDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.credit_note_details')]));
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNoteDetails/{id}",
     *      summary="Update the specified CreditNoteDetails in storage",
     *      tags={"CreditNoteDetails"},
     *      description="Update CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteDetails")
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
     *                  ref="#/definitions/CreditNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CreditNoteDetails $creditNoteDetails */
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.credit_note_details')]));
        }

        $creditNoteDetails = $this->creditNoteDetailsRepository->update($input, $id);

        return $this->sendResponse($creditNoteDetails->toArray(), trans('custom.update', ['attribute' => trans('custom.credit_note_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNoteDetails/{id}",
     *      summary="Remove the specified CreditNoteDetails from storage",
     *      tags={"CreditNoteDetails"},
     *      description="Delete CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetails",
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
        /** @var CreditNoteDetails $creditNoteDetails */
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.credit_note_details')]));
        }
        $creditNoteDetails->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.credit_note_details')]));
    }

    public function addcreditNoteDetails(Request $request)
    {
        $input = $request->all();
        $messages = [
            'companySystemID.required' => 'Company is required.',
            'creditNoteAutoID.required' => 'ID is required.',
            'glCode.required' => 'GL Account is required.'
        ];

        $validator = \Validator::make($request->all(), [
            'companySystemID' => 'required|numeric|min:1',
            'creditNoteAutoID' => 'required|numeric|min:1',
            'glCode' => 'required|numeric|min:1'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companySystemID = $request['companySystemID'];
        $creditNoteAutoID = $request['creditNoteAutoID'];
        $glCode = $request['glCode'];


        /*get master*/
        $master = CreditNote::select('*')->where('creditNoteAutoID', $creditNoteAutoID)->first();
        $myCurr = $master->customerCurrencyID;               /*currencyID*/
        //$companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();

        if ($master->projectID) {
            $inputData['detail_project_id'] = $master->projectID;
        }

        $inputData['creditNoteAutoID'] = $creditNoteAutoID;
        $inputData['companyID'] = $master->companyID;
        $inputData['companySystemID'] = $companySystemID;
        $inputData['customerID'] = $master->customerID;
        $inputData['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $inputData['glCode'] = $chartOfAccount->AccountCode;
        $inputData['glCodeDes'] = $chartOfAccount->AccountDescription;
        $inputData['comments'] = $master->comments;
        $inputData['creditAmountCurrency'] = $myCurr;
        $inputData['creditAmountCurrencyER'] = '';
        $inputData['creditAmount'] = 0;
        $inputData['localCurrency'] = $master->localCurrencyID;
        $inputData['localCurrencyER'] = $master->localCurrencyER;
        $inputData['localAmount'] = 0;
        $inputData['comRptCurrency'] = $master->companyReportingCurrencyID;
        $inputData['comRptCurrencyER'] = $master->companyReportingER;
        if ($master->FYBiggin) {
            $finYearExp = explode('-', $master->FYBiggin);
            $inputData['budgetYear'] = $finYearExp[0];
        } else {
            $inputData['budgetYear'] = date("Y");
        }
        $inputData['comRptAmount'] = 0;

        $isVATEligible = TaxService::checkCompanyVATEligible($master->companySystemID);

        if ($isVATEligible) {
            $defaultVAT = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
            $inputData['vatSubCategoryID'] = $defaultVAT['vatSubCategoryID'];
            $inputData['VATPercentage'] = $master->VATPercentage;
            $inputData['vatMasterCategoryID'] = $defaultVAT['vatMasterCategoryID'];
        }


        DB::beginTransaction();

        try {
            CreditNoteDetails::create($inputData);
            $details = CreditNoteDetails::select(DB::raw("SUM(creditAmount) as creditAmountTrans"), DB::raw("SUM(localAmount) as creditAmountLocal"), DB::raw("SUM(comRptAmount) as creditAmountRpt"))->where('creditNoteAutoID', $creditNoteAutoID)->first()->toArray();

            CreditNote::where('creditNoteAutoID', $creditNoteAutoID)->update($details);


            DB::commit();
            return $this->sendResponse('s', trans('custom.successfully_created'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getMessage(),500);
        }

    }

    public function creditNoteDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $data = CreditNoteDetails::with(['segment'])->where('creditNoteAutoID', $id)->get();
        return $this->sendResponse($data, trans('custom.delete', ['attribute' => trans('custom.credit_note_details')]));
    }

    public function getAllcontractbyclientbase(Request $request)
    {
        $input = $request->all();
        $customerID = $input['customerID'];
        $companySystemID = $input['companySystemID'];

        $qry = "SELECT contractUID, ContractNumber FROM contractmaster WHERE companySystemID = $companySystemID AND clientID = $customerID";
        $contract = DB::select($qry);

        return $this->sendResponse($contract, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }


    public function updateCreditNote(Request $request)
    {
        $input = $request->all();
        $input=array_except($input, array('segment', 'subCategoryArray', 'subCatgeoryType', 'exempt_vat_portion'));
        $input = $this->convertArrayToValue($input);
        $id = $input['creditNoteDetailsID'];
        array_except($input, 'creditNoteDetailsID');

        $detail = CreditNoteDetails::where('creditNoteDetailsID', $id)->first();


        if (empty($detail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_detail')]), 500);
        }

        $master = CreditNote::select('*')->where('creditNoteAutoID', $detail->creditNoteAutoID)->first();

        $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')
            ->where('contractUID', $input['contractUID'])
            ->first();

        if ($contract) {
            $input['clientContractID'] = $contract->ContractNumber;
        }

        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        if(isset($input['detail_project_id'])){
            $input['detail_project_id'] = $input['detail_project_id'];
        } else {
            $input['detail_project_id'] = null;
        }

        if ($master->FYBiggin) {
            $finYearExp = explode('-', $master->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = date("Y");
        }

        $myCurr = $master->customerCurrencyID;
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

        $input['creditAmountCurrency'] = $master->customerCurrencyID;
        $input['creditAmountCurrencyER'] = 1;
        $totalAmount = $input['creditAmount'];
        $input['creditAmount'] = round($input['creditAmount'], $decimal);
        /**/
        $currency = \Helper::convertAmountToLocalRpt(19, $detail->creditNoteAutoID, $totalAmount);
        $input["comRptAmount"] = $currency['reportingAmount'];
        $input["localAmount"] = $currency['localAmount'];

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;


        if($policy == true){
            $input['localAmount']        = \Helper::roundValue($input['creditAmount'] / $master->localCurrencyER);
            $input['comRptAmount']        = \Helper::roundValue($input['creditAmount'] / $master->companyReportingER);
            $input['localCurrencyER' ]    = $master->localCurrencyER;
            $input['comRptCurrencyER']    = $master->companyReportingER;
        }

        // vat amount
        $vatAmount = isset($input['VATAmount'])?$input['VATAmount']:0;
        $currencyVAT = \Helper::convertAmountToLocalRpt(19, $detail->creditNoteAutoID, $vatAmount);
        if($policy == true) {
            $input["VATAmountRpt"] = \Helper::roundValue($vatAmount/$master->companyReportingER);
            $input["VATAmountLocal"] = \Helper::roundValue($vatAmount/$master->localCurrencyER);
        } if($policy == false) {
            $input["VATAmountRpt"] = \Helper::roundValue($currencyVAT['reportingAmount']);
            $input["VATAmountLocal"] = \Helper::roundValue($currencyVAT['localAmount']);
        }
        $input["VATAmount"] = \Helper::roundValue($vatAmount);
        // net amount
        $netAmount = isset($input['netAmount'])?$input['netAmount']:0;
        $currencyNet = \Helper::convertAmountToLocalRpt(19, $detail->creditNoteAutoID, $netAmount);


        if($policy == true) {
            $input["netAmountRpt"] = \Helper::roundValue($netAmount/$master->companyReportingER);
            $input["netAmountLocal"] = \Helper::roundValue($netAmount/$master->localCurrencyER);
        }
        if($policy == false) {
        $input["netAmountRpt"] = $currencyNet['reportingAmount'];
        $input["netAmountLocal"] = $currencyNet['localAmount'];
        }

        if (isset($input['vatMasterCategoryAutoID'])) {
            unset($input['vatMasterCategoryAutoID']);
        }

        if (isset($input['itemPrimaryCode'])) {
            unset($input['itemPrimaryCode']);
        }

        if (isset($input['itemDescription'])) {
            unset($input['itemDescription']);
        }

        if (isset($input['subCategoryArray'])) {
            unset($input['subCategoryArray']);
        }

        CreditNoteDetails::where('creditNoteDetailsID', $id)->update($input);

        return $this->sendResponse('s', trans('custom.update', ['attribute' => trans('custom.credit_note_details')]));

    }


}
