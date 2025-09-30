<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateYearAPIRequest;
use App\Http\Requests\API\UpdateYearAPIRequest;
use App\Models\Year;
use App\Repositories\YearRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class YearController
 * @package App\Http\Controllers\API
 */

class YearAPIController extends AppBaseController
{
    /** @var  YearRepository */
    private $yearRepository;

    public function __construct(YearRepository $yearRepo)
    {
        $this->yearRepository = $yearRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/years",
     *      summary="Get a listing of the Years.",
     *      tags={"Year"},
     *      description="Get all Years",
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
     *                  @SWG\Items(ref="#/definitions/Year")
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
        $this->yearRepository->pushCriteria(new RequestCriteria($request));
        $this->yearRepository->pushCriteria(new LimitOffsetCriteria($request));
        $years = $this->yearRepository->all();

        return $this->sendResponse($years->toArray(), trans('custom.years_retrieved_successfully'));
    }

    /**
     * @param CreateYearAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/years",
     *      summary="Store a newly created Year in storage",
     *      tags={"Year"},
     *      description="Store Year",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Year that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Year")
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
     *                  ref="#/definitions/Year"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateYearAPIRequest $request)
    {
        $input = $request->all();

        $years = $this->yearRepository->create($input);

        return $this->sendResponse($years->toArray(), trans('custom.year_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/years/{id}",
     *      summary="Display the specified Year",
     *      tags={"Year"},
     *      description="Get Year",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Year",
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
     *                  ref="#/definitions/Year"
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
        /** @var Year $year */
        $year = $this->yearRepository->findWithoutFail($id);

        if (empty($year)) {
            return $this->sendError(trans('custom.year_not_found'));
        }

        return $this->sendResponse($year->toArray(), trans('custom.year_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateYearAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/years/{id}",
     *      summary="Update the specified Year in storage",
     *      tags={"Year"},
     *      description="Update Year",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Year",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Year that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Year")
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
     *                  ref="#/definitions/Year"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateYearAPIRequest $request)
    {
        $input = $request->all();

        /** @var Year $year */
        $year = $this->yearRepository->findWithoutFail($id);

        if (empty($year)) {
            return $this->sendError(trans('custom.year_not_found'));
        }

        $year = $this->yearRepository->update($input, $id);

        return $this->sendResponse($year->toArray(), trans('custom.year_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/years/{id}",
     *      summary="Remove the specified Year from storage",
     *      tags={"Year"},
     *      description="Delete Year",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Year",
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
        /** @var Year $year */
        $year = $this->yearRepository->findWithoutFail($id);

        if (empty($year)) {
            return $this->sendError(trans('custom.year_not_found'));
        }

        $year->delete();

        return $this->sendResponse($id, trans('custom.year_deleted_successfully'));
    }
}
