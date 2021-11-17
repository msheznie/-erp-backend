<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSlotMasterWeekDaysAPIRequest;
use App\Http\Requests\API\UpdateSlotMasterWeekDaysAPIRequest;
use App\Models\SlotMasterWeekDays;
use App\Repositories\SlotMasterWeekDaysRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SlotMasterWeekDaysController
 * @package App\Http\Controllers\API
 */

class SlotMasterWeekDaysAPIController extends AppBaseController
{
    /** @var  SlotMasterWeekDaysRepository */
    private $slotMasterWeekDaysRepository;

    public function __construct(SlotMasterWeekDaysRepository $slotMasterWeekDaysRepo)
    {
        $this->slotMasterWeekDaysRepository = $slotMasterWeekDaysRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/slotMasterWeekDays",
     *      summary="Get a listing of the SlotMasterWeekDays.",
     *      tags={"SlotMasterWeekDays"},
     *      description="Get all SlotMasterWeekDays",
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
     *                  @SWG\Items(ref="#/definitions/SlotMasterWeekDays")
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
        $this->slotMasterWeekDaysRepository->pushCriteria(new RequestCriteria($request));
        $this->slotMasterWeekDaysRepository->pushCriteria(new LimitOffsetCriteria($request));
        $slotMasterWeekDays = $this->slotMasterWeekDaysRepository->all();

        return $this->sendResponse($slotMasterWeekDays->toArray(), 'Slot Master Week Days retrieved successfully');
    }

    /**
     * @param CreateSlotMasterWeekDaysAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/slotMasterWeekDays",
     *      summary="Store a newly created SlotMasterWeekDays in storage",
     *      tags={"SlotMasterWeekDays"},
     *      description="Store SlotMasterWeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SlotMasterWeekDays that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SlotMasterWeekDays")
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
     *                  ref="#/definitions/SlotMasterWeekDays"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSlotMasterWeekDaysAPIRequest $request)
    {
        $input = $request->all();

        $slotMasterWeekDays = $this->slotMasterWeekDaysRepository->create($input);

        return $this->sendResponse($slotMasterWeekDays->toArray(), 'Slot Master Week Days saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/slotMasterWeekDays/{id}",
     *      summary="Display the specified SlotMasterWeekDays",
     *      tags={"SlotMasterWeekDays"},
     *      description="Get SlotMasterWeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotMasterWeekDays",
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
     *                  ref="#/definitions/SlotMasterWeekDays"
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
        /** @var SlotMasterWeekDays $slotMasterWeekDays */
        $slotMasterWeekDays = $this->slotMasterWeekDaysRepository->findWithoutFail($id);

        if (empty($slotMasterWeekDays)) {
            return $this->sendError('Slot Master Week Days not found');
        }

        return $this->sendResponse($slotMasterWeekDays->toArray(), 'Slot Master Week Days retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSlotMasterWeekDaysAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/slotMasterWeekDays/{id}",
     *      summary="Update the specified SlotMasterWeekDays in storage",
     *      tags={"SlotMasterWeekDays"},
     *      description="Update SlotMasterWeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotMasterWeekDays",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SlotMasterWeekDays that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SlotMasterWeekDays")
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
     *                  ref="#/definitions/SlotMasterWeekDays"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSlotMasterWeekDaysAPIRequest $request)
    {
        $input = $request->all();

        /** @var SlotMasterWeekDays $slotMasterWeekDays */
        $slotMasterWeekDays = $this->slotMasterWeekDaysRepository->findWithoutFail($id);

        if (empty($slotMasterWeekDays)) {
            return $this->sendError('Slot Master Week Days not found');
        }

        $slotMasterWeekDays = $this->slotMasterWeekDaysRepository->update($input, $id);

        return $this->sendResponse($slotMasterWeekDays->toArray(), 'SlotMasterWeekDays updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/slotMasterWeekDays/{id}",
     *      summary="Remove the specified SlotMasterWeekDays from storage",
     *      tags={"SlotMasterWeekDays"},
     *      description="Delete SlotMasterWeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotMasterWeekDays",
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
        /** @var SlotMasterWeekDays $slotMasterWeekDays */
        $slotMasterWeekDays = $this->slotMasterWeekDaysRepository->findWithoutFail($id);

        if (empty($slotMasterWeekDays)) {
            return $this->sendError('Slot Master Week Days not found');
        }

        $slotMasterWeekDays->delete();

        return $this->sendSuccess('Slot Master Week Days deleted successfully');
    }
}
