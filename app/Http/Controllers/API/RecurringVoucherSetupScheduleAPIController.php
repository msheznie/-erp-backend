<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRecurringVoucherSetupScheduleAPIRequest;
use App\Http\Requests\API\UpdateRecurringVoucherSetupScheduleAPIRequest;
use App\Models\RecurringVoucherSetupSchedule;
use App\Repositories\RecurringVoucherSetupScheduleRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Http\Controllers\API\DocumentAttachmentsAPIController;
use App\Http\Controllers\API\JvDetailAPIController;
use App\Http\Controllers\API\JvMasterAPIController;
use App\Models\DocumentAttachments;
use App\Models\JvMaster;
use App\Models\RecurringVoucherSetupScheDet;
use App\Models\RecurringVoucherScheduleError;
use App\Services\JournalVoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class RecurringVoucherSetupScheduleController
 * @package App\Http\Controllers\API
 */

class RecurringVoucherSetupScheduleAPIController extends AppBaseController
{
    /** @var  RecurringVoucherSetupScheduleRepository */
    private $recurringVoucherSetupScheduleRepository;

    public function __construct(RecurringVoucherSetupScheduleRepository $recurringVoucherSetupScheduleRepo)
    {
        $this->recurringVoucherSetupScheduleRepository = $recurringVoucherSetupScheduleRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupSchedules",
     *      summary="getRecurringVoucherSetupScheduleList",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Get all RecurringVoucherSetupSchedules",
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
     *                  @OA\Items(ref="#/definitions/RecurringVoucherSetupSchedule")
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
        $this->recurringVoucherSetupScheduleRepository->pushCriteria(new RequestCriteria($request));
        $this->recurringVoucherSetupScheduleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $recurringVoucherSetupSchedules = $this->recurringVoucherSetupScheduleRepository->all();

        return $this->sendResponse($recurringVoucherSetupSchedules->toArray(), trans('custom.recurring_voucher_setup_schedules_retrieved_succes'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/recurringVoucherSetupSchedules",
     *      summary="createRecurringVoucherSetupSchedule",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Create RecurringVoucherSetupSchedule",
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
     *                  ref="#/definitions/RecurringVoucherSetupSchedule"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRecurringVoucherSetupScheduleAPIRequest $request)
    {
        $input = $request->all();

        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->create($input);

        return $this->sendResponse($recurringVoucherSetupSchedule->toArray(), trans('custom.recurring_voucher_setup_schedule_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupSchedules/{id}",
     *      summary="getRecurringVoucherSetupScheduleItem",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Get RecurringVoucherSetupSchedule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupSchedule",
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
     *                  ref="#/definitions/RecurringVoucherSetupSchedule"
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
        /** @var RecurringVoucherSetupSchedule $recurringVoucherSetupSchedule */
        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupSchedule)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_schedule_not_found'));
        }

        return $this->sendResponse($recurringVoucherSetupSchedule->toArray(), trans('custom.recurring_voucher_setup_schedule_retrieved_success'));
    }

    public function dateCalculate($oldDate,$newDate){
        $new = Carbon::createFromFormat('m/d/Y', $newDate);
        $oldDate->day = $new->day;
        $oldDate->month = $new->month;

        return $oldDate->format('Y-m-d');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/recurringVoucherSetupSchedules/{id}",
     *      summary="updateRecurringVoucherSetupSchedule",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Update RecurringVoucherSetupSchedule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupSchedule",
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
     *                  ref="#/definitions/RecurringVoucherSetupSchedule"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRecurringVoucherSetupScheduleAPIRequest $request)
    {
        try{
            $input = $request->all();

            if(isset($input[0]['id'])) {
                $scheduleIdsInCroneJobProgress = RecurringVoucherSetupSchedule::where('rrvSetupScheduleAutoID',$input[0]['id'])->first();
                $msg = trans('custom.recurring_voucher_setup_schedule_processing_now');

                if($scheduleIdsInCroneJobProgress  && $scheduleIdsInCroneJobProgress->isInProccess)
                {
                    return $this->sendError($msg);
                }
            }

            /** @var RecurringVoucherSetupSchedule $recurringVoucherSetupSchedule */

            if(isset($input['state']) && $input['state'] == 'toggleStop'){
                $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->find($id);

                if (empty($recurringVoucherSetupSchedule)) {
                    return $this->sendError(trans('custom.recurring_voucher_setup_schedule_not_found'));
                }

                if($recurringVoucherSetupSchedule->isInProccess) {
                    return $this->sendError(trans('custom.recurring_voucher_setup_schedule_processing_now'));
                }

                $rrvCurrentState = $recurringVoucherSetupSchedule->stopYN;

                $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository
                    ->update(['stopYN' => !$rrvCurrentState], $id);
                return $this->sendResponse(
                    $recurringVoucherSetupSchedule->toArray(),
                    $rrvCurrentState ? trans('custom.rrv_schedule_continue_successfully') : trans('custom.rrv_schedule_stopped_successfully')
                );
            }
            else{
                
                $employee = \Helper::getEmployeeInfo();

                if(count($input) == 1){
                    $newDate = Carbon::parse($input[0]['date']);

                    if ($newDate->isBefore(Carbon::today())) {
                        return $this->sendError(trans('custom.past_dates_not_allow'));
                    }
                    if($input[0]['id'] == 0){
                        $recurringVoucherSetupSchedule = RecurringVoucherSetupSchedule::where('recurringVoucherAutoId',$id)
                            ->where('rrvGeneratedYN',0)->where('stopYN',0)->get();

                        foreach ($recurringVoucherSetupSchedule as $data){
                            $tempDate = $this->dateCalculate($data->processDate,$input[0]['date']);

                            if(!$data->isInProccess) {
                                $data->update([
                                    'processDate' => new Carbon($tempDate),
                                    'modifiedPc' => gethostname(),
                                    'modifiedUser' => $employee->empID,
                                    'modifiedUserSystemID' => $employee->employeeSystemID
                                ]);
                            }

                        }
                    }
                    else{
                        $recurringVoucherSetupSchedule = RecurringVoucherSetupSchedule::where('recurringVoucherAutoId',$id)
                            ->where('rrvSetupScheduleAutoID',$input[0]['id'])->first();

                        $tempDate = $this->dateCalculate($recurringVoucherSetupSchedule->processDate,$input[0]['date']);

                        if(!$recurringVoucherSetupSchedule->isInProccess) {
                            $recurringVoucherSetupSchedule->update([
                                'processDate' => new Carbon($tempDate),
                                'modifiedPc' => gethostname(),
                                'modifiedUser' => $employee->empID,
                                'modifiedUserSystemID' => $employee->employeeSystemID
                            ]);
                        }

                    }
                }
                else{
                    foreach ($input as $schedule){
                        $newDate = Carbon::parse($schedule['date']);

                        if ($newDate->isBefore(Carbon::today())) {
                            return $this->sendError(trans('custom.past_dates_not_allow'));
                        }
                        $recurringVoucherSetupSchedule = RecurringVoucherSetupSchedule::where('recurringVoucherAutoId',$id)
                            ->where('rrvSetupScheduleAutoID',$schedule['id'])->first();

                        $tempDate = $this->dateCalculate($recurringVoucherSetupSchedule->processDate,$schedule['date']);

                        if(!$recurringVoucherSetupSchedule->isInProccess) {
                            $recurringVoucherSetupSchedule->update([
                                'processDate' => new Carbon($tempDate),
                                'modifiedPc' => gethostname(),
                                'modifiedUser' => $employee->empID,
                                'modifiedUserSystemID' => $employee->employeeSystemID
                            ]);
                        }

                    }
                }

                return $this->sendResponse($recurringVoucherSetupSchedule, trans('custom.rrv_rescheduled_successfully'));
            }

        }catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/recurringVoucherSetupSchedules/{id}",
     *      summary="deleteRecurringVoucherSetupSchedule",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Delete RecurringVoucherSetupSchedule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupSchedule",
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
        /** @var RecurringVoucherSetupSchedule $recurringVoucherSetupSchedule */
        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupSchedule)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_schedule_not_found'));
        }

        $recurringVoucherSetupSchedule->delete();

        return $this->sendSuccess(trans('custom.recurring_voucher_setup_schedule_deleted_successfully'));
    }

    public function getAllRecurringVoucherSchedules(Request $request)
    {
        $masterId = $request['recurringVoucherAutoId'];

        $output = $this->recurringVoucherSetupScheduleRepository->where('recurringVoucherAutoId',$masterId)->with(['generateDocument','detail'])->get();

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function recurringVoucherSchedulesAllStop(Request $request)
    {
        try{
            $masterId = $request['recurringVoucherAutoId'];

            $employee = \Helper::getEmployeeInfo();

            $output = $this->recurringVoucherSetupScheduleRepository->where('recurringVoucherAutoId',$masterId)->where('isInProccess',0)
                ->where('rrvGeneratedYN', 0)
                ->update([
                    'stopYN' => 1,
                    'modifiedPc' => gethostname(),
                    'modifiedUser' => $employee->empID,
                    'modifiedUserSystemID' => $employee->employeeSystemID
                ]);

            return $this->sendResponse($output, trans('custom.all_schedules_stopped_successfully'));
        }catch (\Exception $e){
            return $this->sendError(trans('custom.try_again'));
        }
    }

    public function getJVScheduleData(Request $request)
    {
        try {
            $companySystemID = $request['companyId'];
            
            $tomorrowDate = Carbon::tomorrow()->format('d-m-y');
            
            $output = RecurringVoucherSetupSchedule::whereHas('master', function($query) use ($companySystemID) {
                    $query->where('approved', -1)
                          ->where('companySystemID', $companySystemID);
                })
                ->whereDate('processDate', $tomorrowDate)
                ->where('stopYN', 0)
                ->where('rrvGeneratedYN', 0)
                ->with(['master'])
                ->get();

            $data['scheduleDate'] = Carbon::tomorrow()->format('d-m-Y');
            $data['jvScheduleData'] = $output;
            return $this->sendResponse($data, trans('custom.record_retrieved_successfully'));
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function getNotPostedScheduleData(Request $request)
    {
        try {
            $companySystemID = $request['companyId'];
            $today = Carbon::today();
            
            $schedules = RecurringVoucherSetupSchedule::whereHas('master', function($query) use ($companySystemID) {
                    $query->where('approved', -1)
                          ->where('companySystemID', $companySystemID);
                })
                ->where('processDate', '<=', $today)
                ->where('rrvGeneratedYN', 0)
                ->where('stopYN', 0)
                ->with(['master']);
            
            return \DataTables::of($schedules)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search')['value'])) {
                        $searchValue = $request->input('search')['value'];
                        $searchValue = addcslashes($searchValue, '%_\\');
                        $query->where(function($q) use ($searchValue) {
                            $q->whereHas('master', function($masterQuery) use ($searchValue) {
                                $masterQuery->where('RRVcode', 'like', '%' . $searchValue . '%')
                                          ->orWhere('narration', 'like', '%' . $searchValue . '%');
                            })
                            ->orWhere('amount', 'like', '%' . $searchValue . '%')
                            ->orWhere('processDate', 'like', '%' . $searchValue . '%');
                        });
                    }
                })
                ->addIndexColumn()
                ->addColumn('hasError', function($schedule) {
                    $error = RecurringVoucherScheduleError::where('rrvSetupScheduleAutoID', $schedule->rrvSetupScheduleAutoID)
                                                          ->first();
                    return !empty($error);
                })
                ->addColumn('failReason', function($schedule) {
                    $error = RecurringVoucherScheduleError::where('rrvSetupScheduleAutoID', $schedule->rrvSetupScheduleAutoID)
                                                          ->first();
                    
                    if (!empty($error)) {
                        return trans('custom.technical_system_errors');
                    } else {
                        return trans('custom.schedule_created_for_past_period');
                    }
                })
                ->addColumn('errorMessages', function($schedule) {
                    $errors = RecurringVoucherScheduleError::where('rrvSetupScheduleAutoID', $schedule->rrvSetupScheduleAutoID)
                                                         ->pluck('errorMessage')
                                                         ->toArray();
                    return $errors;
                })
                ->make(true);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function postNotPostedSchedule(Request $request)
    {
        try {
            $rrvSetupScheduleAutoID = $request['rrvSetupScheduleAutoID'];
            
       
            if (empty($rrvSetupScheduleAutoID)) {
                return $this->sendError(trans('custom.rrv_setup_schedule_id_required'));
            }
            
            if (!is_numeric($rrvSetupScheduleAutoID)) {
                return $this->sendError(trans('custom.invalid_rrv_setup_schedule_id'));
            }
            
            $schedule = RecurringVoucherSetupSchedule::find($rrvSetupScheduleAutoID);
            
            if (empty($schedule)) {
                return $this->sendError(trans('custom.recurring_voucher_setup_schedule_not_found'));
            }
            
            
            $result = JournalVoucherService::postRecurringVoucherSchedule($rrvSetupScheduleAutoID);
            
            if ($result['success']) {
                return $this->sendResponse($result, $result['message']);
            } else {
                return $this->sendError($result['message']);
            }
            
        } catch (\Exception $e) {
            Log::error("Post recurring voucher schedule error: {$e->getMessage()}");
            return $this->sendError($e->getMessage());
        }
    }

}