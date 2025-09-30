<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRecurringVoucherSetupScheDetAPIRequest;
use App\Http\Requests\API\UpdateRecurringVoucherSetupScheDetAPIRequest;
use App\Models\RecurringVoucherSetupScheDet;
use App\Repositories\RecurringVoucherSetupScheDetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Repositories\UserRepository;
use App\Models\RecurringVoucherSetup;
use Illuminate\Support\Facades\Auth;
use App\Models\ChartOfAccount;
use App\Models\RecurringVoucherSetupSchedule;
use DB;
use App\Models\SegmentMaster;

/**
 * Class RecurringVoucherSetupScheDetController
 * @package App\Http\Controllers\API
 */

class RecurringVoucherSetupScheDetAPIController extends AppBaseController
{
    /** @var  RecurringVoucherSetupScheDetRepository */
    private $recurringVoucherSetupScheDetRepository;
    private $userRepository;

    public function __construct(RecurringVoucherSetupScheDetRepository $recurringVoucherSetupScheDetRepo,UserRepository $userRepository)
    {
        $this->recurringVoucherSetupScheDetRepository = $recurringVoucherSetupScheDetRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupScheDets",
     *      summary="getRecurringVoucherSetupScheDetList",
     *      tags={"RecurringVoucherSetupScheDet"},
     *      description="Get all RecurringVoucherSetupScheDets",
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
     *                  @OA\Items(ref="#/definitions/RecurringVoucherSetupScheDet")
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
        $this->recurringVoucherSetupScheDetRepository->pushCriteria(new RequestCriteria($request));
        $this->recurringVoucherSetupScheDetRepository->pushCriteria(new LimitOffsetCriteria($request));
        $recurringVoucherSetupScheDets = $this->recurringVoucherSetupScheDetRepository->all();

        return $this->sendResponse($recurringVoucherSetupScheDets->toArray(), trans('custom.recurring_voucher_setup_sche_dets_retrieved_succes'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/recurringVoucherSetupScheDets",
     *      summary="createRecurringVoucherSetupScheDet",
     *      tags={"RecurringVoucherSetupScheDet"},
     *      description="Create RecurringVoucherSetupScheDet",
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
     *                  ref="#/definitions/RecurringVoucherSetupScheDet"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRecurringVoucherSetupScheDetAPIRequest $request)
    {
        $input = $request->all();
            
        if (!isset($input['details']) || !is_array($input['details'])) {
            return $this->sendError(trans('custom.invalid_input_details_is_required_and_should_be_an'));
        }
        $details = $input['details'];

        if (!isset($input['items']) || !is_array($input['items'])) {
            return $this->sendError(trans('custom.invalid_input_items_is_required_and_should_be_an_a'));
        }
        $items = $input['items'];
        
        $isSingle = $items['isSingleItem'];

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $rrvMaster = RecurringVoucherSetup::find($items['recurringVoucherAutoId']);

        if (empty($rrvMaster)) {
            return $this->sendError(trans('custom.recurring_voucher_not_found'));
        }

        $data['recurringVoucherAutoId'] = $items['recurringVoucherAutoId'];
        $data['documentSystemID'] = $rrvMaster->documentSystemID;
        $data['documentID'] = $rrvMaster->documentID;
        $data['companySystemID'] = $rrvMaster->companySystemID;
        $data['createdPcID'] = gethostname();
        $data['createdUserID'] = isset($user->employee['empID']) ? $user->employee['empID'] : null;
        $data['createdUserSystemID'] =  isset($user->employee['employeeSystemID']) ? $user->employee['employeeSystemID'] : null;
        $data['currencyID'] = $rrvMaster->currencyID;
        $data['comments'] = $rrvMaster->narration;
        if(!$isSingle)
        {
            RecurringVoucherSetupScheDet::where('recurringVoucherAutoId', $items['recurringVoucherAutoId'])
                ->where('companySystemID', $items['companySystemID'])
                ->whereHas('sheduleId', function ($query) {
                    $query->where('stopYN', 0)
                          ->where('rrvGeneratedYN', 0);
                })
                ->delete();

            $sheduleDetails = RecurringVoucherSetupSchedule::where('stopYN', 0)
                                                         ->where('rrvGeneratedYN', 0)
                                                         ->where('recurringVoucherAutoId', $items['recurringVoucherAutoId'])->get();

            foreach($sheduleDetails as $sheduleDetail)
            {
                 $data['recurringVoucherSheduleAutoId'] = $sheduleDetail['rrvSetupScheduleAutoID'];
                 $this->updateShedule($data,$details);
            }
        }
        else
        {   
            $singleID = $items['singleId'];
            RecurringVoucherSetupScheDet::where('recurringVoucherAutoId', $items['recurringVoucherAutoId'])->where('recurringVoucherSheduleAutoId', $singleID)
                                                    ->where('companySystemID', $items['companySystemID'])
                                                    ->delete();
            
             $data['recurringVoucherSheduleAutoId'] = $singleID;
             $this->updateShedule($data,$details);
        }

        return $this->sendResponse(true, trans('custom.recurring_voucher_setup_sche_det_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupScheDets/{id}",
     *      summary="getRecurringVoucherSetupScheDetItem",
     *      tags={"RecurringVoucherSetupScheDet"},
     *      description="Get RecurringVoucherSetupScheDet",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupScheDet",
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
     *                  ref="#/definitions/RecurringVoucherSetupScheDet"
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
        /** @var RecurringVoucherSetupScheDet $recurringVoucherSetupScheDet */
        $recurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupScheDet)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_sche_det_not_found'));
        }

        return $this->sendResponse($recurringVoucherSetupScheDet->toArray(), trans('custom.recurring_voucher_setup_sche_det_retrieved_success'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/recurringVoucherSetupScheDets/{id}",
     *      summary="updateRecurringVoucherSetupScheDet",
     *      tags={"RecurringVoucherSetupScheDet"},
     *      description="Update RecurringVoucherSetupScheDet",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupScheDet",
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
     *                  ref="#/definitions/RecurringVoucherSetupScheDet"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRecurringVoucherSetupScheDetAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var RecurringVoucherSetupScheDet $recurringVoucherSetupScheDet */
        $recurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupScheDet)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_sche_det_not_found'));
        }

         $rrvMaster = RecurringVoucherSetup::find($input['recurringVoucherAutoId']);

        if (empty($rrvMaster)) {
            return $this->sendError(trans('custom.recurring_voucher_not_found'));
        }

        $data['isChecked'] = $input['isChecked'];
        $data['creditAmount'] = $input['creditAmount'];
        $data['debitAmount'] = $input['debitAmount'];
        if ($input['creditAmount'] == '') {
            $data['creditAmount'] = 0;
        }
        if ($input['debitAmount'] == '') {
            $data['debitAmount'] = 0;
        }
        
        $data['serviceLineSystemID'] = $input['serviceLineSystemID'];
        if (isset($input['serviceLineSystemID'])) {

            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError(trans('custom.department_not_found'));
                }

                if ($checkDepartmentActive->isActive == 0) {
                    return $this->sendError(trans('custom.please_select_active_department'), 500, $serviceLineError);
                }

                $data['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        $recurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepository->update($data, $id);

        return $this->sendResponse($recurringVoucherSetupScheDet->toArray(), trans('custom.recurring_voucher_setup_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/recurringVoucherSetupScheDets/{id}",
     *      summary="deleteRecurringVoucherSetupScheDet",
     *      tags={"RecurringVoucherSetupScheDet"},
     *      description="Delete RecurringVoucherSetupScheDet",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupScheDet",
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
        /** @var RecurringVoucherSetupScheDet $recurringVoucherSetupScheDet */
        $recurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupScheDet)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_sche_det_not_found'));
        }

        $recurringVoucherSetupScheDet->delete();

        return $this->sendSuccess(trans('custom.recurring_voucher_setup_sche_det_deleted_successfully'));
    }

    public function getSheduleDetails(Request $request)
    {
        $input = $request->all();

        $isSingle = $input['isSingle'];
        $data = [];
        if(!$isSingle)
        {
            $data = RecurringVoucherSetupScheDet::where('recurringVoucherAutoId', $input['voucherId'])
                        ->where('companySystemID', $input['companySystemID'])
                        ->where('recurringVoucherSheduleAutoId', function ($query) use ($input) {
                            $query->select('recurringVoucherSheduleAutoId')
                                ->from('recurring_voucher_shedule_det')
                                ->where('recurringVoucherAutoId', $input['voucherId'])
                                ->where('companySystemID', $input['companySystemID'])
                                ->orderBy('id')
                                ->limit(1);
                                })
                                ->get();


        }           
        else{
            $data =  RecurringVoucherSetupScheDet::where('recurringVoucherAutoId',($input['voucherId']))->where('recurringVoucherSheduleAutoId',($input['sheduleId']))->where('companySystemID',($input['companySystemID']))->get();
        }

        return $this->sendResponse($data->toArray(), trans('custom.recurringvouchersetupschedet_updated_successfully'));
    }

    public function updateShedule($data,$details)
    {
        foreach($details as $detail)
        {
            if($detail['isChecked'])
            {
                $chartOfAccount = ChartOfAccount::find($detail['chartOfAccountSystemID']);
                if (empty($chartOfAccount)) {
                    continue;
                }
                $data['isChecked'] = $detail['isChecked'];
                $data['creditAmount'] = $detail['creditAmount'];
                $data['debitAmount'] = $detail['debitAmount'];
                if ($detail['creditAmount'] == '') {
                    $data['creditAmount'] = 0;
                }
                if ($detail['debitAmount'] == '') {
                    $data['debitAmount'] = 0;
                }

                $data['serviceLineSystemID'] = is_array($detail['serviceLineSystemID']) 
                    ? $detail['serviceLineSystemID'][0] 
                    : $detail['serviceLineSystemID'];

                if (isset($data['serviceLineSystemID'])) {

                    if ($data['serviceLineSystemID'] > 0) {
                        $checkDepartmentActive = SegmentMaster::find($data['serviceLineSystemID']);
                        if (empty($checkDepartmentActive)) {
                            continue;
                        }

                        $data['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
                    }
                }

                $data['chartOfAccountSystemID'] = $detail['chartOfAccountSystemID'];
                $data['glAccount'] = $chartOfAccount->AccountCode;
                $data['glAccountDescription'] = $chartOfAccount->AccountDescription;
                
                RecurringVoucherSetupScheDet::create($data);
            }

        }
    }
}
