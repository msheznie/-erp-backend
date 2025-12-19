<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderTypeAPIRequest;
use App\Http\Requests\API\UpdateTenderTypeAPIRequest;
use App\Models\TenderType;
use App\Repositories\TenderTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderTypeController
 * @package App\Http\Controllers\API
 */

class TenderTypeAPIController extends AppBaseController
{
    /** @var  TenderTypeRepository */
    private $tenderTypeRepository;

    public function __construct(TenderTypeRepository $tenderTypeRepo)
    {
        $this->tenderTypeRepository = $tenderTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderTypes",
     *      summary="Get a listing of the TenderTypes.",
     *      tags={"TenderType"},
     *      description="Get all TenderTypes",
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
     *                  @SWG\Items(ref="#/definitions/TenderType")
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
        $this->tenderTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderTypes = $this->tenderTypeRepository->all();

        return $this->sendResponse($tenderTypes->toArray(), trans('custom.tender_types_retrieved_successfully'));
    }

    /**
     * @param CreateTenderTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderTypes",
     *      summary="Store a newly created TenderType in storage",
     *      tags={"TenderType"},
     *      description="Store TenderType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderType")
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
     *                  ref="#/definitions/TenderType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderTypeAPIRequest $request)
    {
        $input = $request->all();

        $tenderType = $this->tenderTypeRepository->create($input);

        return $this->sendResponse($tenderType->toArray(), trans('custom.tender_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderTypes/{id}",
     *      summary="Display the specified TenderType",
     *      tags={"TenderType"},
     *      description="Get TenderType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderType",
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
     *                  ref="#/definitions/TenderType"
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
        /** @var TenderType $tenderType */
        $tenderType = $this->tenderTypeRepository->findWithoutFail($id);

        if (empty($tenderType)) {
            return $this->sendError(trans('custom.tender_type_not_found'));
        }

        return $this->sendResponse($tenderType->toArray(), trans('custom.tender_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderTypes/{id}",
     *      summary="Update the specified TenderType in storage",
     *      tags={"TenderType"},
     *      description="Update TenderType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderType")
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
     *                  ref="#/definitions/TenderType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderType $tenderType */
        $tenderType = $this->tenderTypeRepository->findWithoutFail($id);

        if (empty($tenderType)) {
            return $this->sendError(trans('custom.tender_type_not_found'));
        }

        $tenderType = $this->tenderTypeRepository->update($input, $id);

        return $this->sendResponse($tenderType->toArray(), trans('custom.tendertype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderTypes/{id}",
     *      summary="Remove the specified TenderType from storage",
     *      tags={"TenderType"},
     *      description="Delete TenderType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderType",
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
        /** @var TenderType $tenderType */
        $tenderType = $this->tenderTypeRepository->findWithoutFail($id);

        if (empty($tenderType)) {
            return $this->sendError(trans('custom.tender_type_not_found'));
        }

        $tenderType->delete();

        return $this->sendSuccess('Tender Type deleted successfully');
    }
}
