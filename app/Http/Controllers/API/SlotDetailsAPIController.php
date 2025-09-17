<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSlotDetailsAPIRequest;
use App\Http\Requests\API\UpdateSlotDetailsAPIRequest;
use App\Http\Requests\API\CalendarSlotDeleteRequest;
use App\Models\SlotDetails;
use App\Repositories\SlotDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SlotDetailsController
 * @package App\Http\Controllers\API
 */

class SlotDetailsAPIController extends AppBaseController
{
    /** @var  SlotDetailsRepository */
    private $slotDetailsRepository;

    public function __construct(SlotDetailsRepository $slotDetailsRepo)
    {
        $this->slotDetailsRepository = $slotDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/slotDetails",
     *      summary="Get a listing of the SlotDetails.",
     *      tags={"SlotDetails"},
     *      description="Get all SlotDetails",
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
     *                  @SWG\Items(ref="#/definitions/SlotDetails")
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
        $this->slotDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->slotDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $slotDetails = $this->slotDetailsRepository->all();

        return $this->sendResponse($slotDetails->toArray(), 'Slot Details retrieved successfully');
    }

    /**
     * @param CreateSlotDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/slotDetails",
     *      summary="Store a newly created SlotDetails in storage",
     *      tags={"SlotDetails"},
     *      description="Store SlotDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SlotDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SlotDetails")
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
     *                  ref="#/definitions/SlotDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSlotDetailsAPIRequest $request)
    {
        $input = $request->all();

        $slotDetails = $this->slotDetailsRepository->create($input);

        return $this->sendResponse($slotDetails->toArray(), 'Slot Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/slotDetails/{id}",
     *      summary="Display the specified SlotDetails",
     *      tags={"SlotDetails"},
     *      description="Get SlotDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotDetails",
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
     *                  ref="#/definitions/SlotDetails"
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
        /** @var SlotDetails $slotDetails */
        $slotDetails = $this->slotDetailsRepository->findWithoutFail($id);

        if (empty($slotDetails)) {
            return $this->sendError('Slot Details not found');
        }

        return $this->sendResponse($slotDetails->toArray(), 'Slot Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSlotDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/slotDetails/{id}",
     *      summary="Update the specified SlotDetails in storage",
     *      tags={"SlotDetails"},
     *      description="Update SlotDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SlotDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SlotDetails")
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
     *                  ref="#/definitions/SlotDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSlotDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SlotDetails $slotDetails */
        $slotDetails = $this->slotDetailsRepository->findWithoutFail($id);

        if (empty($slotDetails)) {
            return $this->sendError('Slot Details not found');
        }

        $slotDetails = $this->slotDetailsRepository->update($input, $id);

        return $this->sendResponse($slotDetails->toArray(), 'SlotDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/slotDetails/{id}",
     *      summary="Remove the specified SlotDetails from storage",
     *      tags={"SlotDetails"},
     *      description="Delete SlotDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SlotDetails",
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
        /** @var SlotDetails $slotDetails */
        $slotDetails = $this->slotDetailsRepository->findWithoutFail($id);

        if (empty($slotDetails)) {
            return $this->sendError('Slot Details not found');
        }

        $slotDetails->delete();

        return $this->sendSuccess('Slot Details deleted successfully');
    }

    public function removeCalenderSlotDetail(Request $request)
    {
        $slotDetailID = $request->input('slotDetailID');
        $deleteSlotDetail = $this->slotDetailsRepository->deleteSlotDetail($slotDetailID);
        if($deleteSlotDetail['success'])
        {
            return $this->sendResponse([], trans('srm_supplier_management.slot_detail_successfully_deleted'));
        } else{
            $statusCode = $deleteSlotDetail['code'] ?? 404;
            return $this->sendError($deleteSlotDetail['message'], $statusCode);
        }
    }

    public function removeDateRangeSlots(CalendarSlotDeleteRequest $request)
    {
        $removeMultipleSlots = $this->slotDetailsRepository->removeMultipleSlots($request);
        if($removeMultipleSlots['status']){
            return $this->sendResponse([], $removeMultipleSlots['message'] ?? trans('srm_supplier_management.slots_deleted_successfully'));
        } else {
            $statusCode = $removeMultipleSlots['code'] ?? 404;
            return $this->sendError($removeMultipleSlots['message'], $statusCode);
        }
    }

    public function getSlotDetailsFormData(Request $request){
        $companyID = $request->input('companyID');
        $filterData = $this->slotDetailsRepository->getSlotDetailsFormData($companyID);
        return $this->sendResponse($filterData, 'Slot details from data retrieved successfully');
    }
}
