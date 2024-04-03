<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRecurringVoucherSetupDetailAPIRequest;
use App\Http\Requests\API\UpdateRecurringVoucherSetupDetailAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Contract;
use App\Models\RecurringVoucherSetup;
use App\Models\RecurringVoucherSetupDetail;
use App\Models\SegmentMaster;
use App\Repositories\RecurringVoucherSetupDetailRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RecurringVoucherSetupDetailController
 * @package App\Http\Controllers\API
 */

class RecurringVoucherSetupDetailAPIController extends AppBaseController
{
    /** @var  RecurringVoucherSetupDetailRepository */
    private $recurringVoucherSetupDetailRepository;
    private $userRepository;

    public function __construct(
        RecurringVoucherSetupDetailRepository $recurringVoucherSetupDetailRepo,
        UserRepository $userRepository
    )
    {
        $this->recurringVoucherSetupDetailRepository = $recurringVoucherSetupDetailRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupDetails",
     *      summary="getRecurringVoucherSetupDetailList",
     *      tags={"RecurringVoucherSetupDetail"},
     *      description="Get all RecurringVoucherSetupDetails",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/RecurringVoucherSetupDetail")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->recurringVoucherSetupDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->recurringVoucherSetupDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $recurringVoucherSetupDetails = $this->recurringVoucherSetupDetailRepository->all();

        return $this->sendResponse($recurringVoucherSetupDetails->toArray(), 'Recurring Voucher Setup Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/recurringVoucherSetupDetails",
     *      summary="createRecurringVoucherSetupDetail",
     *      tags={"RecurringVoucherSetupDetail"},
     *      description="Create RecurringVoucherSetupDetail",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/RecurringVoucherSetupDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRecurringVoucherSetupDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $rrvMaster = RecurringVoucherSetup::find($input['recurringVoucherAutoId']);

        if (empty($rrvMaster)) {
            return $this->sendError('Recurring Voucher not found');
        }
        $messages = [
            'currencyID' => 'Currency is required',
        ];
        $validator = \Validator::make($rrvMaster->toArray(), [
            'currencyID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($messages, 422);
        }

        $input['documentSystemID'] = $rrvMaster->documentSystemID;
        $input['documentID'] = $rrvMaster->documentID;
        $input['companySystemID'] = $rrvMaster->companySystemID;

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return $this->sendError('Chart of Account not found');
        }

        $input['glAccount'] = $chartOfAccount->AccountCode;
        $input['glAccountDescription'] = $chartOfAccount->AccountDescription;

        $input['currencyID'] = $rrvMaster->currencyID;
        $input['comments'] = $rrvMaster->narration;

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $jvDetails = $this->recurringVoucherSetupDetailRepository->create($input);

        return $this->sendResponse($jvDetails->toArray(), 'RRV Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupDetails/{id}",
     *      summary="getRecurringVoucherSetupDetailItem",
     *      tags={"RecurringVoucherSetupDetail"},
     *      description="Get RecurringVoucherSetupDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/RecurringVoucherSetupDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var RecurringVoucherSetupDetail $recurringVoucherSetupDetail */
        $recurringVoucherSetupDetail = $this->recurringVoucherSetupDetailRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupDetail)) {
            return $this->sendError('Recurring Voucher Setup Detail not found');
        }

        return $this->sendResponse($recurringVoucherSetupDetail->toArray(), 'Recurring Voucher Setup Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/recurringVoucherSetupDetails/{id}",
     *      summary="updateRecurringVoucherSetupDetail",
     *      tags={"RecurringVoucherSetupDetail"},
     *      description="Update RecurringVoucherSetupDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/RecurringVoucherSetupDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRecurringVoucherSetupDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'currency_by', 'chartofaccount']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');

        $rrvDetail = $this->recurringVoucherSetupDetailRepository->findWithoutFail($id);

        if (empty($rrvDetail)) {
            return $this->sendError('RRV Detail not found');
        }

        $rrvMaster = RecurringVoucherSetup::find($input['recurringVoucherAutoId']);

        if (empty($rrvMaster)) {
            return $this->sendError('Recurring Voucher not found');
        }

        if ($input['creditAmount'] == '') {
            $input['creditAmount'] = 0;
        }
        if ($input['debitAmount'] == '') {
            $input['debitAmount'] = 0;
        }

        if (isset($input['serviceLineSystemID'])) {

            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError('Department not found');
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->$rrvDetail->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    return $this->sendError('Please select an active department', 500, $serviceLineError);
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        if (isset($input['contractUID'])) {

            $input['clientContractID'] = NULL;

            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')
                ->where('contractUID', $input['contractUID'])
                ->first();

            if(!empty($contract)) {
                $input['clientContractID'] = $contract['ContractNumber'];
            }

        }

        $rrvDetail = $this->recurringVoucherSetupDetailRepository->update($input, $id);

        return $this->sendResponse($rrvDetail->toArray(), 'RRVDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/recurringVoucherSetupDetails/{id}",
     *      summary="deleteRecurringVoucherSetupDetail",
     *      tags={"RecurringVoucherSetupDetail"},
     *      description="Delete RecurringVoucherSetupDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var RecurringVoucherSetupDetail $recurringVoucherSetupDetail */
        $recurringVoucherSetupDetail = $this->recurringVoucherSetupDetailRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupDetail)) {
            return $this->sendError('Recurring Voucher Setup Detail not found');
        }

        $recurringVoucherSetupDetail->delete();

        return $this->sendResponse($id,'Recurring Voucher Setup Detail deleted successfully');
    }

    public function getRecurringVoucherDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['recurringVoucherAutoId'];

        $items = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)
            ->with(['segment', 'currency_by', 'chartofaccount'])
            ->orderBy('rrvDetailAutoId', 'ASC')
            ->get();

        return $this->sendResponse($items->toArray(), 'RRV Detail retrieved successfully');
    }

    public function getRecurringVoucherContracts(Request $request)
    {
        $input = $request->all();
        $jvDetailAutoID = isset($input['rrvDetailAutoId']) ? $input['rrvDetailAutoId'] : 0;
        $contract = [];

        $detail = RecurringVoucherSetupDetail::where('rrvDetailAutoId', $jvDetailAutoID)->first();

        if(!empty($detail)){
            $master = RecurringVoucherSetup::where('recurringVoucherAutoId', $detail->recurringVoucherAutoId)->first();

            $contractID = 0;
            if ($detail->contractUID != '' && $detail->contractUID != 0) {
                $contractID = $detail->contractUID;
            }

            if(!empty($master)){
                $contract = Contract::select('contractUID', 'ContractNumber')
                    ->where('companySystemID',$master->companySystemID)
                    ->get();
            }
        }

        return $this->sendResponse($contract, 'Record retrived successfully');
    }

    public function recurringVoucherDeleteAllDetails(Request $request)
    {
        $input = $request->all();

        $rrvMasterAutoId = $input['recurringVoucherAutoId'];

        $rrvMaster = RecurringVoucherSetup::find($rrvMasterAutoId);

        if (empty($rrvMaster)) {
            return $this->sendError('Recurring Voucher not found');
        }

        $detailExistAll = $this->recurringVoucherSetupDetailRepository->where('recurringVoucherAutoId', $rrvMasterAutoId)->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {
            $this->recurringVoucherSetupDetailRepository->where('recurringVoucherAutoId', $rrvMasterAutoId)->delete();
        }

        return $this->sendResponse($rrvMasterAutoId, 'Details deleted successfully');
    }
}
