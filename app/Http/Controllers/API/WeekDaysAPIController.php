<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWeekDaysAPIRequest;
use App\Http\Requests\API\UpdateWeekDaysAPIRequest;
use App\Models\WeekDays;
use App\Repositories\WeekDaysRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WeekDaysController
 * @package App\Http\Controllers\API
 */

class WeekDaysAPIController extends AppBaseController
{
    /** @var  WeekDaysRepository */
    private $weekDaysRepository;

    public function __construct(WeekDaysRepository $weekDaysRepo)
    {
        $this->weekDaysRepository = $weekDaysRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/weekDays",
     *      summary="Get a listing of the WeekDays.",
     *      tags={"WeekDays"},
     *      description="Get all WeekDays",
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
     *                  @SWG\Items(ref="#/definitions/WeekDays")
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
        $this->weekDaysRepository->pushCriteria(new RequestCriteria($request));
        $this->weekDaysRepository->pushCriteria(new LimitOffsetCriteria($request));
        $weekDays = $this->weekDaysRepository->all();

        return $this->sendResponse($weekDays->toArray(), trans('custom.week_days_retrieved_successfully'));
    }

    /**
     * @param CreateWeekDaysAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/weekDays",
     *      summary="Store a newly created WeekDays in storage",
     *      tags={"WeekDays"},
     *      description="Store WeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WeekDays that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WeekDays")
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
     *                  ref="#/definitions/WeekDays"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWeekDaysAPIRequest $request)
    {
        $input = $request->all();

        $weekDays = $this->weekDaysRepository->create($input);

        return $this->sendResponse($weekDays->toArray(), trans('custom.week_days_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/weekDays/{id}",
     *      summary="Display the specified WeekDays",
     *      tags={"WeekDays"},
     *      description="Get WeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WeekDays",
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
     *                  ref="#/definitions/WeekDays"
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
        /** @var WeekDays $weekDays */
        $weekDays = $this->weekDaysRepository->findWithoutFail($id);

        if (empty($weekDays)) {
            return $this->sendError(trans('custom.week_days_not_found'));
        }

        return $this->sendResponse($weekDays->toArray(), trans('custom.week_days_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateWeekDaysAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/weekDays/{id}",
     *      summary="Update the specified WeekDays in storage",
     *      tags={"WeekDays"},
     *      description="Update WeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WeekDays",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WeekDays that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WeekDays")
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
     *                  ref="#/definitions/WeekDays"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWeekDaysAPIRequest $request)
    {
        $input = $request->all();

        /** @var WeekDays $weekDays */
        $weekDays = $this->weekDaysRepository->findWithoutFail($id);

        if (empty($weekDays)) {
            return $this->sendError(trans('custom.week_days_not_found'));
        }

        $weekDays = $this->weekDaysRepository->update($input, $id);

        return $this->sendResponse($weekDays->toArray(), trans('custom.weekdays_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/weekDays/{id}",
     *      summary="Remove the specified WeekDays from storage",
     *      tags={"WeekDays"},
     *      description="Delete WeekDays",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WeekDays",
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
        /** @var WeekDays $weekDays */
        $weekDays = $this->weekDaysRepository->findWithoutFail($id);

        if (empty($weekDays)) {
            return $this->sendError(trans('custom.week_days_not_found'));
        }

        $weekDays->delete();

        return $this->sendSuccess('Week Days deleted successfully');
    }
}
