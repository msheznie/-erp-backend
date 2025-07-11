<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSlotMasterAPIRequest;
use App\Http\Requests\API\UpdateSlotMasterAPIRequest;
use App\Models\Company;
use App\Models\SlotMaster;
use App\Repositories\SlotMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Location;
use App\Models\WarehouseMaster;
use App\Models\WeekDays;
use Carbon\Carbon;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Class SlotMasterController
 * @package App\Http\Controllers\API
 */

class SlotMasterAPIController extends AppBaseController
{
    /** @var  SlotMasterRepository */
    private $slotMasterRepository;

    public function __construct(SlotMasterRepository $slotMasterRepo)
    {
        $this->slotMasterRepository = $slotMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/slotMasters",
     *      summary="Get a listing of the SlotMasters.",
     *      tags={"SlotMaster"},
     *      description="Get all SlotMasters",
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
     *                  @SWG\Items(ref="#/definitions/SlotMaster")
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
        $this->slotMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->slotMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $slotMasters = $this->slotMasterRepository->all();

        return $this->sendResponse($slotMasters->toArray(), 'Slot Masters retrieved successfully');
    }

    /**
     * @param CreateSlotMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/slotMasters",
     *      summary="Store a newly created SlotMaster in storage",
     *      tags={"SlotMaster"},
     *      description="Store SlotMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SlotMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SlotMaster")
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
     *                  ref="#/definitions/SlotMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSlotMasterAPIRequest $request)
    {
        $input = $request->all();

        $slotMaster = $this->slotMasterRepository->create($input);

        return $this->sendResponse($slotMaster->toArray(), 'Slot Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/slotMasters/{id}",
     *      summary="Display the specified SlotMaster",
     *      tags={"SlotMaster"},
     *      description="Get SlotMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotMaster",
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
     *                  ref="#/definitions/SlotMaster"
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
        /** @var SlotMaster $slotMaster */
        $slotMaster = $this->slotMasterRepository->findWithoutFail($id);

        if (empty($slotMaster)) {
            return $this->sendError('Slot Master not found');
        }

        return $this->sendResponse($slotMaster->toArray(), 'Slot Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSlotMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/slotMasters/{id}",
     *      summary="Update the specified SlotMaster in storage",
     *      tags={"SlotMaster"},
     *      description="Update SlotMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SlotMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SlotMaster")
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
     *                  ref="#/definitions/SlotMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSlotMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SlotMaster $slotMaster */
        $slotMaster = $this->slotMasterRepository->findWithoutFail($id);

        if (empty($slotMaster)) {
            return $this->sendError('Slot Master not found');
        }

        $slotMaster = $this->slotMasterRepository->update($input, $id);

        return $this->sendResponse($slotMaster->toArray(), 'SlotMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/slotMasters/{id}",
     *      summary="Remove the specified SlotMaster from storage",
     *      tags={"SlotMaster"},
     *      description="Delete SlotMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotMaster",
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
        /** @var SlotMaster $slotMaster */
        $slotMaster = $this->slotMasterRepository->findWithoutFail($id);

        if (empty($slotMaster)) {
            return $this->sendError('Slot Master not found');
        }

        $slotMaster->delete();

        return $this->sendSuccess('Slot Master deleted successfully');
    }
    public function saveCalanderSlots(Request $request)
    {
        $calanderSlots = $this->slotMasterRepository->saveCalanderSlots($request);
        if ($calanderSlots['status']) {
            return $this->sendResponse([], 'Successfully updated');
        } else {
            $statusCode = isset($calanderSlots['code']) ? $calanderSlots['code'] : 404;
            return $this->sendError($calanderSlots['message'], $statusCode);
        }
    }
    public function getFormDataCalander(Request $request)
    {
        try{
            $calendarSlots = $this->slotMasterRepository->getFormDataCalendar($request);
            return $this->sendResponse($calendarSlots, 'Record retrieved successfully');
        } catch (\Exception $ex){
            return $this->sendError($ex->getMessage(), 500);
        }
    }
    public function getCalanderSlotData(Request $request)
    {
        try{
            $calendarSlots = $this->slotMasterRepository->getCalendarSlotData($request);
            return $this->sendResponse($calendarSlots, 'Record retrieved successfully');
        } catch (\Exception $ex){
            return $this->sendError($ex->getMessage(), 500);
        }
    }

    private function getStartDate(){

    }

    public function clanderSlotDateRangeValidation(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = [
            'wareHouse.required' => 'Warehouse is required.',
            'dateFrom.required' => 'From Date is required.',
            'dateTo.required' => 'To Date is required.'
        ];

        $validator = \Validator::make($input, [
            'wareHouse' => 'required',
            'dateFrom' => 'required',
            'dateTo' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $wareHouse =  $input['wareHouse'];
        $fromDate =  new Carbon($input['dateFrom']);
        $toDate =  new Carbon($input['dateTo']);

        if (isset($fromDate) && isset($toDate) && ($toDate->format('Y-m-d') < $fromDate->format('Y-m-d'))) {
            return $this->sendError('To Date must be greater than the From Date');
        }

        $begin = new DateTime($fromDate);
        $end = clone $begin;
        $end->modify($toDate);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);
        $weekDayArr = [];
        $new = [];
        $weekDays =  WeekDays::get();
        foreach ($weekDays as $val) {
            foreach ($daterange as $date) {
                if (!in_array($val['description'], $weekDayArr) && $val['description'] == $date->format("l")) {
                    array_push($weekDayArr, $val);
                }
            }
        }
        return $this->sendResponse($weekDayArr, 'Record retrieved successfully');
    }


    public function clanderSlotMasterData(Request $request)
    {
        $input = $request->all();
        $slotMasterID = $input['slotMasterID'];
        $companyID = $input['companyID'];
        $isGroupCompany = \Helper::checkIsCompanyGroup($companyID);
        $companyData = [];
        if($isGroupCompany)
        {
            $companiesByGroup = \Helper::getGroupCompany($companyID);
            $companyData = Company::getCompanyList($companiesByGroup);
        }

        $slotMaster = SlotMaster::with(['slot_days' => function ($query) {
            $query->with(['week_days']);
        }])
            ->where('id', $slotMasterID)->first();


        $dateFrom = Carbon::parse($slotMaster['from_date'] ?? null);
        $dateTo = Carbon::parse($slotMaster['to_date'] ?? null);
        $begin =  new DateTime($dateFrom->format('Y-m-d'));
        $end = clone $begin;
        $end->modify($dateTo->format('Y-m-d'));
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);
        $weekDayArr = [];
        $new = [];
        $weekDays =  WeekDays::get();
        foreach ($weekDays as $val) {
            foreach ($daterange as $date) {
                if (!in_array($val['description'], $weekDayArr) && $val['description'] == $date->format("l")) {
                    array_push($weekDayArr, $val);
                }
            }
        }


        return [
            'masterData' => $slotMaster,
            'weekDayArr' =>  $weekDayArr,
            'company' => $companyData,
            'is_group_company' => $isGroupCompany
        ];
    }

    public function removeCalanderSlot(Request $request)
    {
        $input = $request->all();
        $slotMasterId = $input['slotMasterId'];
        $calanderSlots = $this->slotMasterRepository->deleteSlot($slotMasterId);
        if ($calanderSlots['status']) {
            return $this->sendResponse([], 'Successfully deleted');
        } else {
            $statusCode = isset($calanderSlots['code']) ? $calanderSlots['code'] : 404;
            return $this->sendError($calanderSlots['message'], $statusCode);
        }
    }
}
