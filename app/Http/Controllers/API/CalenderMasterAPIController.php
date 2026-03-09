<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCalenderMasterAPIRequest;
use App\Http\Requests\API\UpdateCalenderMasterAPIRequest;
use App\Models\CalenderMaster;
use App\Repositories\CalenderMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CalenderMasterController
 * @package App\Http\Controllers\API
 */

class CalenderMasterAPIController extends AppBaseController
{
    /** @var  CalenderMasterRepository */
    private $calenderMasterRepository;

    public function __construct(CalenderMasterRepository $calenderMasterRepo)
    {
        $this->calenderMasterRepository = $calenderMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/calenderMasters",
     *      summary="Get a listing of the CalenderMasters.",
     *      tags={"CalenderMaster"},
     *      description="Get all CalenderMasters",
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
     *                  @SWG\Items(ref="#/definitions/CalenderMaster")
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
        $this->calenderMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->calenderMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $calenderMasters = $this->calenderMasterRepository->all();

        return $this->sendResponse($calenderMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.calender_masters')]));
    }

    /**
     * @param CreateCalenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/calenderMasters",
     *      summary="Store a newly created CalenderMaster in storage",
     *      tags={"CalenderMaster"},
     *      description="Store CalenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CalenderMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CalenderMaster")
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
     *                  ref="#/definitions/CalenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCalenderMasterAPIRequest $request)
    {
        $input = $request->all();

        $calenderMaster = $this->calenderMasterRepository->create($input);

        return $this->sendResponse($calenderMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.calender_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/calenderMasters/{id}",
     *      summary="Display the specified CalenderMaster",
     *      tags={"CalenderMaster"},
     *      description="Get CalenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalenderMaster",
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
     *                  ref="#/definitions/CalenderMaster"
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
        /** @var CalenderMaster $calenderMaster */
        $calenderMaster = $this->calenderMasterRepository->findWithoutFail($id);

        if (empty($calenderMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.calender_masters')]));
        }

        return $this->sendResponse($calenderMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.calender_masters')]));
    }

    /**
     * @param int $id
     * @param UpdateCalenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/calenderMasters/{id}",
     *      summary="Update the specified CalenderMaster in storage",
     *      tags={"CalenderMaster"},
     *      description="Update CalenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalenderMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CalenderMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CalenderMaster")
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
     *                  ref="#/definitions/CalenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCalenderMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CalenderMaster $calenderMaster */
        $calenderMaster = $this->calenderMasterRepository->findWithoutFail($id);

        if (empty($calenderMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.calender_masters')]));
        }

        $calenderMaster = $this->calenderMasterRepository->update($input, $id);

        return $this->sendResponse($calenderMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.calender_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/calenderMasters/{id}",
     *      summary="Remove the specified CalenderMaster from storage",
     *      tags={"CalenderMaster"},
     *      description="Delete CalenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalenderMaster",
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
        /** @var CalenderMaster $calenderMaster */
        $calenderMaster = $this->calenderMasterRepository->findWithoutFail($id);

        if (empty($calenderMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.calender_masters')]));
        }

        $calenderMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.calender_masters')]));
    }
}
