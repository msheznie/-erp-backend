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

}
