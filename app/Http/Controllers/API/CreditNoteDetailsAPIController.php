<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteDetailsAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteDetailsAPIRequest;
use App\Models\CreditNoteDetails;
use App\Models\CreditNote;
use App\Models\chartOfAccount;
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

        return $this->sendResponse($creditNoteDetails->toArray(), 'Credit Note Details retrieved successfully');
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

        return $this->sendResponse($creditNoteDetails->toArray(), 'Credit Note Details saved successfully');
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
            return $this->sendError('Credit Note Details not found');
        }

        return $this->sendResponse($creditNoteDetails->toArray(), 'Credit Note Details retrieved successfully');
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
            return $this->sendError('Credit Note Details not found');
        }

        $creditNoteDetails = $this->creditNoteDetailsRepository->update($input, $id);

        return $this->sendResponse($creditNoteDetails->toArray(), 'CreditNoteDetails updated successfully');
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
            return $this->sendError('Credit Note Details not found');
        }
        $creditNoteDetails->delete();
        $details = CreditNoteDetails::select(DB::raw("IFNULL(SUM(creditAmount),0) as creditAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as creditAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as creditAmountRpt"))->where('creditNoteAutoID', $creditNoteDetails->creditNoteAutoID)->first()->toArray();


        CreditNote::where('creditNoteAutoID', $creditNoteDetails->creditNoteAutoID)->update($details);



        return $this->sendResponse($id, 'Credit Note Details deleted successfully');
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
        $companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();


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
        $inputData['localCurrency'] = $companyCurrency->localcurrency->currencyID;
        $inputData['localCurrencyER'] = $master->localCurrencyER;
        $inputData['localAmount'] = 0;
        $inputData['comRptCurrency'] = $companyCurrency->reportingcurrency->currencyID;
        $inputData['comRptCurrencyER'] = $master->companyReportingER;
        $inputData['comRptAmount'] = 0;


        DB::beginTransaction();

        try {
            CreditNoteDetails::create($inputData);
            $details = CreditNoteDetails::select(DB::raw("SUM(creditAmount) as creditAmountTrans"), DB::raw("SUM(localAmount) as creditAmountLocal"), DB::raw("SUM(comRptAmount) as creditAmountRpt"))->where('creditNoteAutoID', $creditNoteAutoID)->first()->toArray();

            CreditNote::where('creditNoteAutoID', $creditNoteAutoID)->update($details);


            DB::commit();
            return $this->sendResponse('s', 'successfully created');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Error Occured !');
        }

    }

    public function creditNoteDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $data = CreditNoteDetails::where('creditNoteAutoID', $id)->get();
        return $this->sendResponse($data, 'Credit Note Details deleted successfully');
    }

    public function creditNotegetcontract(Request $request)
    {
        $input = $request->all();
        $creditNoteDetailsID = $input['creditNoteDetailsID'];
        $detail = CreditNoteDetails::where('creditNoteDetailsID', $creditNoteDetailsID)->first();
        $master = CreditNote::where('creditNoteAutoID', $detail->creditNoteAutoID)->first();

        $contractID = 0;
        if ($detail->contractUID != '' && $detail->contractUID != 0) {
            $contractID = $detail->contractUID;

        }

        $qry = "SELECT * FROM ( SELECT contractUID, ContractNumber FROM contractmaster WHERE ServiceLineCode = '{$detail->serviceLineCode}' AND companySystemID = $master->companySystemID AND clientID = $master->customerID UNION ALL SELECT contractUID, ContractNumber FROM contractmaster WHERE contractUID = $contractID ) t GROUP BY contractUID, ContractNumber";
        $contract = DB::select($qry);


        return $this->sendResponse($contract, 'Contract deleted successfully');
    }


    public function updateCreditNote(Request $request){
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $id = $input['creditNoteDetailsID'];
        array_except($input,'creditNoteDetailsID');

        $detail = CreditNoteDetails::where('creditNoteDetailsID', $id)->first();


        if (empty($detail)) {
            return $this->sendError('Customer Invoice Direct Detail not found',500);
        }

        $master = CreditNote::select('*')->where('creditNoteAutoID', $detail->creditNoteAutoID)->first();

        if ($input['contractUID'] != $detail->contractUID) {
            $input['clientContractID']=NULL;
            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')->where('CompanyID', $detail->companyID)->where('contractUID', $input['contractUID'])->first();
            $input['clientContractID'] = $contract->ContractNumber;


        }

        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            $input['clientContractID'] = NULL;
            $input['contractUID'] = NULL;
        }



        if ($input['creditAmount'] != $detail->creditAmount) {
            $myCurr = $master->customerCurrencyID;               /*currencyID*/
            $companyCurrency = \Helper::companyCurrency($myCurr);
            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

            $input['creditAmountCurrency'] = $master->customerCurrencyID;
            $input['creditAmountCurrencyER'] = 1;
             $totalAmount =$input['creditAmount'];
            $input['creditAmount'] = round($input['creditAmount'], $decimal);
            /**/
            $currency = \Helper::convertAmountToLocalRpt(19,$detail->creditNoteAutoID,$totalAmount);
            $input["comRptAmount"]=$currency['reportingAmount'];
            $input["localAmount"]=$currency['localAmount'];



        }


        DB::beginTransaction();

        try {

            $x=CreditNoteDetails::where('creditNoteDetailsID', $id)->update($input);
            $details = CreditNoteDetails::select(DB::raw("SUM(creditAmount) as creditAmountTrans"), DB::raw("SUM(localAmount) as creditAmountLocal"), DB::raw("SUM(comRptAmount) as creditAmountRpt"))->where('creditNoteAutoID', $detail->creditNoteAutoID)->first()->toArray();
            CreditNote::where('creditNoteAutoID', $detail->creditNoteAutoID)->update($details);



            DB::commit();
            return $this->sendResponse('s', 'successfully created');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception);
        }


    }


}
