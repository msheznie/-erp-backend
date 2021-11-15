<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSlotMasterAPIRequest;
use App\Http\Requests\API\UpdateSlotMasterAPIRequest;
use App\Models\SlotMaster;
use App\Repositories\SlotMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Location;
use App\Models\WarehouseMaster;
use App\Models\WeekDays;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

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
        $companyID = $request['companyID'];
        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyID);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $weekDays = WeekDays::all();

        $wareHouseLocation = $wareHouseLocation->get();

        $output = array(
            'wareHouseLocation' => $wareHouseLocation,
            'weekDays' => $weekDays
        );
        return $this->sendResponse($output, 'Record retrieved successfully');
    }
    public function getCalanderSlotData(Request $request)
    {
        $input = $request->all();
        $slot = new SlotMaster();
        $companyID  = $input['companyID'];
        $wareHouseID = $input['warhouse'];
        $data = $slot->getSlotData($companyID, $wareHouseID);
        return $this->sendResponse($data, 'Record retrieved successfully');
    }
}
