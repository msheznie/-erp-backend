<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReligionAPIRequest;
use App\Http\Requests\API\UpdateReligionAPIRequest;
use App\Models\Religion;
use App\Repositories\ReligionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReligionController
 * @package App\Http\Controllers\API
 */

class ReligionAPIController extends AppBaseController
{
    /** @var  ReligionRepository */
    private $religionRepository;

    public function __construct(ReligionRepository $religionRepo)
    {
        $this->religionRepository = $religionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/religions",
     *      summary="Get a listing of the Religions.",
     *      tags={"Religion"},
     *      description="Get all Religions",
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
     *                  @SWG\Items(ref="#/definitions/Religion")
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
        $this->religionRepository->pushCriteria(new RequestCriteria($request));
        $this->religionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $religions = $this->religionRepository->all();

        return $this->sendResponse($religions->toArray(), trans('custom.religions_retrieved_successfully'));
    }

    /**
     * @param CreateReligionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/religions",
     *      summary="Store a newly created Religion in storage",
     *      tags={"Religion"},
     *      description="Store Religion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Religion that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Religion")
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
     *                  ref="#/definitions/Religion"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReligionAPIRequest $request)
    {
        $input = $request->all();

        $religion = $this->religionRepository->create($input);

        return $this->sendResponse($religion->toArray(), trans('custom.religion_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/religions/{id}",
     *      summary="Display the specified Religion",
     *      tags={"Religion"},
     *      description="Get Religion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Religion",
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
     *                  ref="#/definitions/Religion"
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
        /** @var Religion $religion */
        $religion = $this->religionRepository->findWithoutFail($id);

        if (empty($religion)) {
            return $this->sendError(trans('custom.religion_not_found'));
        }

        return $this->sendResponse($religion->toArray(), trans('custom.religion_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReligionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/religions/{id}",
     *      summary="Update the specified Religion in storage",
     *      tags={"Religion"},
     *      description="Update Religion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Religion",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Religion that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Religion")
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
     *                  ref="#/definitions/Religion"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReligionAPIRequest $request)
    {
        $input = $request->all();

        /** @var Religion $religion */
        $religion = $this->religionRepository->findWithoutFail($id);

        if (empty($religion)) {
            return $this->sendError(trans('custom.religion_not_found'));
        }

        $religion = $this->religionRepository->update($input, $id);

        return $this->sendResponse($religion->toArray(), trans('custom.religion_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/religions/{id}",
     *      summary="Remove the specified Religion from storage",
     *      tags={"Religion"},
     *      description="Delete Religion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Religion",
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
        /** @var Religion $religion */
        $religion = $this->religionRepository->findWithoutFail($id);

        if (empty($religion)) {
            return $this->sendError(trans('custom.religion_not_found'));
        }

        $religion->delete();

        return $this->sendResponse($id, trans('custom.religion_deleted_successfully'));
    }
}
