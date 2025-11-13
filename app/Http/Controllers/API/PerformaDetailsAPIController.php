<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePerformaDetailsAPIRequest;
use App\Http\Requests\API\UpdatePerformaDetailsAPIRequest;
use App\Models\PerformaDetails;
use App\Repositories\PerformaDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PerformaDetailsController
 * @package App\Http\Controllers\API
 */

class PerformaDetailsAPIController extends AppBaseController
{
    /** @var  PerformaDetailsRepository */
    private $performaDetailsRepository;

    public function __construct(PerformaDetailsRepository $performaDetailsRepo)
    {
        $this->performaDetailsRepository = $performaDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/performaDetails",
     *      summary="Get a listing of the PerformaDetails.",
     *      tags={"PerformaDetails"},
     *      description="Get all PerformaDetails",
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
     *                  @SWG\Items(ref="#/definitions/PerformaDetails")
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
        $this->performaDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->performaDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $performaDetails = $this->performaDetailsRepository->all();

        return $this->sendResponse($performaDetails->toArray(), trans('custom.performa_details_retrieved_successfully'));
    }

    /**
     * @param CreatePerformaDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/performaDetails",
     *      summary="Store a newly created PerformaDetails in storage",
     *      tags={"PerformaDetails"},
     *      description="Store PerformaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PerformaDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PerformaDetails")
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
     *                  ref="#/definitions/PerformaDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePerformaDetailsAPIRequest $request)
    {
        $input = $request->all();

        $performaDetails = $this->performaDetailsRepository->create($input);

        return $this->sendResponse($performaDetails->toArray(), trans('custom.performa_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/performaDetails/{id}",
     *      summary="Display the specified PerformaDetails",
     *      tags={"PerformaDetails"},
     *      description="Get PerformaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaDetails",
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
     *                  ref="#/definitions/PerformaDetails"
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
        /** @var PerformaDetails $performaDetails */
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            return $this->sendError(trans('custom.performa_details_not_found'));
        }

        return $this->sendResponse($performaDetails->toArray(), trans('custom.performa_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePerformaDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/performaDetails/{id}",
     *      summary="Update the specified PerformaDetails in storage",
     *      tags={"PerformaDetails"},
     *      description="Update PerformaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PerformaDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PerformaDetails")
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
     *                  ref="#/definitions/PerformaDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePerformaDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PerformaDetails $performaDetails */
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            return $this->sendError(trans('custom.performa_details_not_found'));
        }

        $performaDetails = $this->performaDetailsRepository->update($input, $id);

        return $this->sendResponse($performaDetails->toArray(), trans('custom.performadetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/performaDetails/{id}",
     *      summary="Remove the specified PerformaDetails from storage",
     *      tags={"PerformaDetails"},
     *      description="Delete PerformaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaDetails",
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
        /** @var PerformaDetails $performaDetails */
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            return $this->sendError(trans('custom.performa_details_not_found'));
        }

        $performaDetails->delete();

        return $this->sendResponse($id, trans('custom.performa_details_deleted_successfully'));
    }
}
