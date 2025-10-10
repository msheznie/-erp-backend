<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMonthlyDeclarationsTypesAPIRequest;
use App\Http\Requests\API\UpdateMonthlyDeclarationsTypesAPIRequest;
use App\Models\MonthlyDeclarationsTypes;
use App\Repositories\MonthlyDeclarationsTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MonthlyDeclarationsTypesController
 * @package App\Http\Controllers\API
 */

class MonthlyDeclarationsTypesAPIController extends AppBaseController
{
    /** @var  MonthlyDeclarationsTypesRepository */
    private $monthlyDeclarationsTypesRepository;

    public function __construct(MonthlyDeclarationsTypesRepository $monthlyDeclarationsTypesRepo)
    {
        $this->monthlyDeclarationsTypesRepository = $monthlyDeclarationsTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyDeclarationsTypes",
     *      summary="Get a listing of the MonthlyDeclarationsTypes.",
     *      tags={"MonthlyDeclarationsTypes"},
     *      description="Get all MonthlyDeclarationsTypes",
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
     *                  @SWG\Items(ref="#/definitions/MonthlyDeclarationsTypes")
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
        $this->monthlyDeclarationsTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->monthlyDeclarationsTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $monthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepository->all();

        return $this->sendResponse($monthlyDeclarationsTypes->toArray(), trans('custom.monthly_declarations_types_retrieved_successfully'));
    }

    /**
     * @param CreateMonthlyDeclarationsTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/monthlyDeclarationsTypes",
     *      summary="Store a newly created MonthlyDeclarationsTypes in storage",
     *      tags={"MonthlyDeclarationsTypes"},
     *      description="Store MonthlyDeclarationsTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyDeclarationsTypes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyDeclarationsTypes")
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
     *                  ref="#/definitions/MonthlyDeclarationsTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMonthlyDeclarationsTypesAPIRequest $request)
    {
        $input = $request->all();

        $monthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepository->create($input);

        return $this->sendResponse($monthlyDeclarationsTypes->toArray(), trans('custom.monthly_declarations_types_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyDeclarationsTypes/{id}",
     *      summary="Display the specified MonthlyDeclarationsTypes",
     *      tags={"MonthlyDeclarationsTypes"},
     *      description="Get MonthlyDeclarationsTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyDeclarationsTypes",
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
     *                  ref="#/definitions/MonthlyDeclarationsTypes"
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
        /** @var MonthlyDeclarationsTypes $monthlyDeclarationsTypes */
        $monthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepository->findWithoutFail($id);

        if (empty($monthlyDeclarationsTypes)) {
            return $this->sendError(trans('custom.monthly_declarations_types_not_found'));
        }

        return $this->sendResponse($monthlyDeclarationsTypes->toArray(), trans('custom.monthly_declarations_types_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMonthlyDeclarationsTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/monthlyDeclarationsTypes/{id}",
     *      summary="Update the specified MonthlyDeclarationsTypes in storage",
     *      tags={"MonthlyDeclarationsTypes"},
     *      description="Update MonthlyDeclarationsTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyDeclarationsTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyDeclarationsTypes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyDeclarationsTypes")
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
     *                  ref="#/definitions/MonthlyDeclarationsTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMonthlyDeclarationsTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var MonthlyDeclarationsTypes $monthlyDeclarationsTypes */
        $monthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepository->findWithoutFail($id);

        if (empty($monthlyDeclarationsTypes)) {
            return $this->sendError(trans('custom.monthly_declarations_types_not_found'));
        }

        $monthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepository->update($input, $id);

        return $this->sendResponse($monthlyDeclarationsTypes->toArray(), trans('custom.monthlydeclarationstypes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/monthlyDeclarationsTypes/{id}",
     *      summary="Remove the specified MonthlyDeclarationsTypes from storage",
     *      tags={"MonthlyDeclarationsTypes"},
     *      description="Delete MonthlyDeclarationsTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyDeclarationsTypes",
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
        /** @var MonthlyDeclarationsTypes $monthlyDeclarationsTypes */
        $monthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepository->findWithoutFail($id);

        if (empty($monthlyDeclarationsTypes)) {
            return $this->sendError(trans('custom.monthly_declarations_types_not_found'));
        }

        $monthlyDeclarationsTypes->delete();

        return $this->sendSuccess('Monthly Declarations Types deleted successfully');
    }
}
