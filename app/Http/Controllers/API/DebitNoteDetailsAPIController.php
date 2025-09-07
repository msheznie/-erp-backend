<?php
/**
 * =============================================
 * -- File Name : DebitNoteDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DebitNoteDetails
 * -- Author : Mohamed Nazir
 * -- Create date : 16 - August 2018
 * -- Description : This file contains the all CRUD for Debit Note
 * -- REVISION HISTORY
 * -- Date: 05-September 2018 By: Fayas Description: Added new function getDetailsByDebitNote()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Requests\API\CreateDebitNoteDetailsAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteDetailsAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\SegmentMaster;
use App\Repositories\DebitNoteDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DebitNoteDetailsController
 * @package App\Http\Controllers\API
 */
class DebitNoteDetailsAPIController extends AppBaseController
{
    /** @var  DebitNoteDetailsRepository */
    private $debitNoteDetailsRepository;

    public function __construct(DebitNoteDetailsRepository $debitNoteDetailsRepo)
    {
        $this->debitNoteDetailsRepository = $debitNoteDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteDetails",
     *      summary="Get a listing of the DebitNoteDetails.",
     *      tags={"DebitNoteDetails"},
     *      description="Get all DebitNoteDetails",
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
     *                  @SWG\Items(ref="#/definitions/DebitNoteDetails")
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
        $this->debitNoteDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNoteDetails = $this->debitNoteDetailsRepository->all();

        return $this->sendResponse($debitNoteDetails->toArray(), trans('custom.debit_note_details_retrieved_successfully'));
    }

    /**
     * @param CreateDebitNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNoteDetails",
     *      summary="Store a newly created DebitNoteDetails in storage",
     *      tags={"DebitNoteDetails"},
     *      description="Store DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteDetails")
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
     *                  ref="#/definitions/DebitNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $companySystemID = $input['companySystemID'];
        $debitNote = DebitNote::find($input['debitNoteAutoID']);

        $type =  $input['type'];


     
        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

  
        $validator = \Validator::make($debitNote->toArray(), [
            'supplierID' => ['required_if:type,1|numeric|min:1'],
            'empID' => ['required_if:type,2|numeric|min:1'],
            'supplierTransactionCurrencyID' => 'required|numeric|min:1',
            'comments' => 'required',
        ],
        [
            'empID.required_if' => 'please select the employee',
            'supplierID.required_if' => 'please select the supplier',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
       
        $alreadyAdded = DebitNote::where('debitNoteAutoID', $debitNote->debitNoteAutoID)
            ->whereHas('detail', function ($query) use ($input) {
                $query->where('chartOfAccountSystemID', $input['chartOfAccountSystemID']);
            })
            ->first();

        if ($alreadyAdded) {
            //return $this->sendError("Selected item is already added. Please check again", 500);
        }

        $checkWhether = DebitNote::where('debitNoteAutoID', '!=', $debitNote->debitNoteAutoID)
            ->where('companySystemID', $companySystemID)
            ->select([
                'debitNoteAutoID',
                'companySystemID',
                'debitNoteCode',
                'approved'
            ])
            ->groupBy(
                'debitNoteAutoID',
                'companySystemID',
                'approved'
            )
            ->whereHas('detail', function ($query) use ($companySystemID, $input) {
                $query->where('chartOfAccountSystemID', $input['chartOfAccountSystemID']);
            })
            ->where('approved', 0)
            ->first();


        if (!empty($checkWhether)) {
            //return $this->sendError("There is a Debit Note (" . $checkWhether->debitNoteCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'));
        }

        if ($debitNote->projectID) {
            $input['detail_project_id'] = $debitNote->projectID;
        }


        $input['companySystemID'] = $debitNote->companySystemID;
        $input['companyID'] = $debitNote->companyID;
        $input['supplierID'] = $debitNote->supplierID;

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return $this->sendError(trans('custom.chart_of_account_not_found_1'));
        }

        $input['glCode'] = $chartOfAccount->AccountCode;
        $input['glCodeDes'] = $chartOfAccount->AccountDescription;

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, 0);

        $input['debitAmountCurrency'] = $debitNote->supplierTransactionCurrencyID;
        $input['debitAmountCurrencyER'] = 1;
        $input['localCurrency'] = $debitNote->localCurrencyID;
        $input['comRptCurrency'] = $debitNote->companyReportingCurrencyID;

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;


        if($policy == true){
            $input['localCurrencyER' ]    = $debitNote->localCurrencyER;
            $input['comRptCurrencyER']    = $debitNote->companyReportingER;
        }

        if($policy == false) {
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        }

        if ($debitNote->FYBiggin) {
            $finYearExp = explode('-', $debitNote->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = date("Y");
        }

        $isVATEligible = TaxService::checkCompanyVATEligible($debitNote->companySystemID);

        if ($isVATEligible) {
            $defaultVAT = TaxService::getDefaultVAT($debitNote->companySystemID, $debitNote->supplierID);
            $input['vatSubCategoryID'] = $defaultVAT['vatSubCategoryID'];
            $input['VATPercentage'] = $defaultVAT['percentage'];
            $input['vatMasterCategoryID'] = $defaultVAT['vatMasterCategoryID'];
        }

        $debitNoteDetails = $this->debitNoteDetailsRepository->create($input);

        return $this->sendResponse($debitNoteDetails->toArray(), trans('custom.debit_note_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteDetails/{id}",
     *      summary="Display the specified DebitNoteDetails",
     *      tags={"DebitNoteDetails"},
     *      description="Get DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetails",
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
     *                  ref="#/definitions/DebitNoteDetails"
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
        /** @var DebitNoteDetails $debitNoteDetails */
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            return $this->sendError(trans('custom.debit_note_details_not_found'));
        }

        return $this->sendResponse($debitNoteDetails->toArray(), trans('custom.debit_note_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNoteDetails/{id}",
     *      summary="Update the specified DebitNoteDetails in storage",
     *      tags={"DebitNoteDetails"},
     *      description="Update DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteDetails")
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
     *                  ref="#/definitions/DebitNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');

        /** @var DebitNoteDetails $debitNoteDetails */
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            return $this->sendError(trans('custom.debit_note_details_not_found'));
        }

        $debitNote = DebitNote::find($input['debitNoteAutoID']);

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        if(isset($input['detail_project_id'])){
            $input['detail_project_id'] = $input['detail_project_id'];
        } else {
            $input['detail_project_id'] = null;
        }

        if (isset($input['serviceLineSystemID'])) {

            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError(trans('custom.department_not_found'));
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->debitNoteDetailsRepository->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    return $this->sendError('Please select an active department', 500, $serviceLineError);
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        $input['debitAmount'] = isset($input['debitAmount']) ?  \Helper::stringToFloat($input['debitAmount']) : 0;
        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, $input['debitAmount']);

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;


        if($policy == true){
            $input['localAmount' ]        = \Helper::roundValue($input['debitAmount'] / $debitNote->localCurrencyER);
            $input['comRptAmount']        = \Helper::roundValue($input['debitAmount'] / $debitNote->companyReportingER);
            $input['localCurrencyER' ]    = $debitNote->localCurrencyER;
            $input['comRptCurrencyER']    = $debitNote->companyReportingER;
        }

        if($policy == false) {
            $input['localAmount'] = $companyCurrencyConversion['localAmount'];
            $input['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        }
        //vat amount currency conversion

        $input['VATAmount'] = isset($input['VATAmount']) ?  \Helper::stringToFloat($input['VATAmount']) : 0;
        $VATCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, $input['VATAmount']);

        if($policy == true) {
            $input['VATAmountLocal'] = $input['VATAmount'] / $debitNote->localCurrencyER;
            $input['VATAmountRpt'] = $input['VATAmount'] / $debitNote->companyReportingER;
        }  if($policy == false) {
        $input['VATAmountLocal'] = $VATCurrencyConversion['localAmount'];
        $input['VATAmountRpt'] = $VATCurrencyConversion['reportingAmount'];
    }

        // total amount currency conversion

        $input['netAmount'] = isset($input['netAmount']) ?  \Helper::stringToFloat($input['netAmount']) : 0;
        $totalCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, $input['netAmount']);

        if($policy == true) {
            $input['netAmountLocal'] = $input['netAmount']/ $debitNote->localCurrencyER;
            $input['netAmountRpt'] = $input['netAmount'] / $debitNote->companyReportingER;
        } if($policy == false) {

        $input['netAmountLocal'] = $totalCurrencyConversion['localAmount'];
        $input['netAmountRpt'] = $totalCurrencyConversion['reportingAmount'];
    }


        $debitNoteDetails = $this->debitNoteDetailsRepository->update($input, $id);

        $amount = DebitNoteDetails::where('debitNoteAutoID', $debitNoteDetails->debitNoteAutoID)
            ->sum('debitAmount');
        $companyCurrencyConversionMaster = \Helper::currencyConversion($debitNote->companySystemID, $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, $amount);
        $debitNote['debitAmountTrans'] = $amount;
        $debitNote['debitAmountLocal'] = $companyCurrencyConversionMaster['localAmount'];
        $debitNote['debitAmountRpt']   = $companyCurrencyConversionMaster['reportingAmount'];

        if($policy == true) {

            $debitNote['localCurrencyER'] = $debitNote->localCurrencyER;
            $debitNote['companyReportingER'] = $debitNote->companyReportingER;

        } if($policy == false) {

            $debitNote['localCurrencyER'] = $companyCurrencyConversionMaster['trasToLocER'];
            $debitNote['companyReportingER'] = $companyCurrencyConversionMaster['trasToRptER'];

        }


        $vatAmount = DebitNoteDetails::where('debitNoteAutoID', $debitNoteDetails->debitNoteAutoID)
            ->sum('VATAmount');
        //vat amount currency conversion

        $debitNote['VATAmount'] = $vatAmount;
        $VATCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, $vatAmount);
        if($policy == true) {
            $debitNote['VATAmountLocal'] = $vatAmount / $debitNote->localCurrencyER;
            $debitNote['VATAmountRpt'] = $vatAmount / $debitNote->companyReportingER;
        }  if($policy == false) {
            $debitNote['VATAmountLocal'] = $VATCurrencyConversion['localAmount'];
            $debitNote['VATAmountRpt'] = $VATCurrencyConversion['reportingAmount'];
        }

        $totalNetAmount = DebitNoteDetails::where('debitNoteAutoID', $debitNoteDetails->debitNoteAutoID)
            ->sum('netAmount');
        // total amount currency conversion

        $debitNote['netAmount'] = $totalNetAmount;
        $totalCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $debitNote->supplierTransactionCurrencyID, $debitNote->supplierTransactionCurrencyID, $totalNetAmount);
        if($policy == true) {
            $debitNote['netAmountLocal'] = $totalNetAmount / $debitNote->localCurrencyER;
            $debitNote['netAmountRpt'] = $totalNetAmount / $debitNote->companyReportingER;
        } if($policy == false) {

        $debitNote['netAmountLocal'] = $totalCurrencyConversion['localAmount'];
        $debitNote['netAmountRpt'] = $totalCurrencyConversion['reportingAmount'];
            }


        $debitNote->save();
        return $this->sendResponse($debitNoteDetails->toArray(), trans('custom.debitnotedetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNoteDetails/{id}",
     *      summary="Remove the specified DebitNoteDetails from storage",
     *      tags={"DebitNoteDetails"},
     *      description="Delete DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetails",
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
        /** @var DebitNoteDetails $debitNoteDetails */
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            return $this->sendError(trans('custom.debit_note_details_not_found'));
        }

        $debitNoteDetails->delete();

        return $this->sendResponse($id, trans('custom.debit_note_details_deleted_successfully'));
    }

    public function getDetailsByDebitNote(Request $request)
    {
        $input = $request->all();
        $id = $input['debitNoteAutoID'];

        $items = DebitNoteDetails::where('debitNoteAutoID', $id)
            ->with(['segment'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.debit_note_details_retrieved_successfully'));
    }

}
