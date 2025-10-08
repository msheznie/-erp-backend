<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGrvDetailsPrnAPIRequest;
use App\Http\Requests\API\UpdateGrvDetailsPrnAPIRequest;
use App\Models\GrvDetailsPrn;
use App\Repositories\GrvDetailsPrnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GrvDetailsPrnController
 * @package App\Http\Controllers\API
 */

class GrvDetailsPrnAPIController extends AppBaseController
{
    /** @var  GrvDetailsPrnRepository */
    private $grvDetailsPrnRepository;

    public function __construct(GrvDetailsPrnRepository $grvDetailsPrnRepo)
    {
        $this->grvDetailsPrnRepository = $grvDetailsPrnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/grvDetailsPrns",
     *      summary="Get a listing of the GrvDetailsPrns.",
     *      tags={"GrvDetailsPrn"},
     *      description="Get all GrvDetailsPrns",
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
     *                  @SWG\Items(ref="#/definitions/GrvDetailsPrn")
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
        $this->grvDetailsPrnRepository->pushCriteria(new RequestCriteria($request));
        $this->grvDetailsPrnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $grvDetailsPrns = $this->grvDetailsPrnRepository->all();

        return $this->sendResponse($grvDetailsPrns->toArray(), trans('custom.grv_details_prns_retrieved_successfully'));
    }

    /**
     * @param CreateGrvDetailsPrnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/grvDetailsPrns",
     *      summary="Store a newly created GrvDetailsPrn in storage",
     *      tags={"GrvDetailsPrn"},
     *      description="Store GrvDetailsPrn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GrvDetailsPrn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GrvDetailsPrn")
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
     *                  ref="#/definitions/GrvDetailsPrn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGrvDetailsPrnAPIRequest $request)
    {
        $input = $request->all();

        $grvDetailsPrn = $this->grvDetailsPrnRepository->create($input);

        return $this->sendResponse($grvDetailsPrn->toArray(), trans('custom.grv_details_prn_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/grvDetailsPrns/{id}",
     *      summary="Display the specified GrvDetailsPrn",
     *      tags={"GrvDetailsPrn"},
     *      description="Get GrvDetailsPrn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvDetailsPrn",
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
     *                  ref="#/definitions/GrvDetailsPrn"
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
        /** @var GrvDetailsPrn $grvDetailsPrn */
        $grvDetailsPrn = $this->grvDetailsPrnRepository->findWithoutFail($id);

        if (empty($grvDetailsPrn)) {
            return $this->sendError(trans('custom.grv_details_prn_not_found'));
        }

        return $this->sendResponse($grvDetailsPrn->toArray(), trans('custom.grv_details_prn_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGrvDetailsPrnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/grvDetailsPrns/{id}",
     *      summary="Update the specified GrvDetailsPrn in storage",
     *      tags={"GrvDetailsPrn"},
     *      description="Update GrvDetailsPrn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvDetailsPrn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GrvDetailsPrn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GrvDetailsPrn")
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
     *                  ref="#/definitions/GrvDetailsPrn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGrvDetailsPrnAPIRequest $request)
    {
        $input = $request->all();

        /** @var GrvDetailsPrn $grvDetailsPrn */
        $grvDetailsPrn = $this->grvDetailsPrnRepository->findWithoutFail($id);

        if (empty($grvDetailsPrn)) {
            return $this->sendError(trans('custom.grv_details_prn_not_found'));
        }

        $grvDetailsPrn = $this->grvDetailsPrnRepository->update($input, $id);

        return $this->sendResponse($grvDetailsPrn->toArray(), trans('custom.grvdetailsprn_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/grvDetailsPrns/{id}",
     *      summary="Remove the specified GrvDetailsPrn from storage",
     *      tags={"GrvDetailsPrn"},
     *      description="Delete GrvDetailsPrn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvDetailsPrn",
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
        /** @var GrvDetailsPrn $grvDetailsPrn */
        $grvDetailsPrn = $this->grvDetailsPrnRepository->findWithoutFail($id);

        if (empty($grvDetailsPrn)) {
            return $this->sendError(trans('custom.grv_details_prn_not_found'));
        }

        $grvDetailsPrn->delete();

        return $this->sendSuccess('Grv Details Prn deleted successfully');
    }
}
