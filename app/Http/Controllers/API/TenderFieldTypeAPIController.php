<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderFieldTypeAPIRequest;
use App\Http\Requests\API\UpdateTenderFieldTypeAPIRequest;
use App\Models\TenderFieldType;
use App\Repositories\TenderFieldTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderFieldTypeController
 * @package App\Http\Controllers\API
 */

class TenderFieldTypeAPIController extends AppBaseController
{
    /** @var  TenderFieldTypeRepository */
    private $tenderFieldTypeRepository;

    public function __construct(TenderFieldTypeRepository $tenderFieldTypeRepo)
    {
        $this->tenderFieldTypeRepository = $tenderFieldTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderFieldTypes",
     *      summary="Get a listing of the TenderFieldTypes.",
     *      tags={"TenderFieldType"},
     *      description="Get all TenderFieldTypes",
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
     *                  @SWG\Items(ref="#/definitions/TenderFieldType")
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
        $this->tenderFieldTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderFieldTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderFieldTypes = $this->tenderFieldTypeRepository->all();

        return $this->sendResponse($tenderFieldTypes->toArray(), trans('custom.tender_field_types_retrieved_successfully'));
    }

    /**
     * @param CreateTenderFieldTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderFieldTypes",
     *      summary="Store a newly created TenderFieldType in storage",
     *      tags={"TenderFieldType"},
     *      description="Store TenderFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderFieldType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderFieldType")
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
     *                  ref="#/definitions/TenderFieldType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderFieldTypeAPIRequest $request)
    {
        $input = $request->all();

        $tenderFieldType = $this->tenderFieldTypeRepository->create($input);

        return $this->sendResponse($tenderFieldType->toArray(), trans('custom.tender_field_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderFieldTypes/{id}",
     *      summary="Display the specified TenderFieldType",
     *      tags={"TenderFieldType"},
     *      description="Get TenderFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderFieldType",
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
     *                  ref="#/definitions/TenderFieldType"
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
        /** @var TenderFieldType $tenderFieldType */
        $tenderFieldType = $this->tenderFieldTypeRepository->findWithoutFail($id);

        if (empty($tenderFieldType)) {
            return $this->sendError(trans('custom.tender_field_type_not_found'));
        }

        return $this->sendResponse($tenderFieldType->toArray(), trans('custom.tender_field_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderFieldTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderFieldTypes/{id}",
     *      summary="Update the specified TenderFieldType in storage",
     *      tags={"TenderFieldType"},
     *      description="Update TenderFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderFieldType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderFieldType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderFieldType")
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
     *                  ref="#/definitions/TenderFieldType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderFieldTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderFieldType $tenderFieldType */
        $tenderFieldType = $this->tenderFieldTypeRepository->findWithoutFail($id);

        if (empty($tenderFieldType)) {
            return $this->sendError(trans('custom.tender_field_type_not_found'));
        }

        $tenderFieldType = $this->tenderFieldTypeRepository->update($input, $id);

        return $this->sendResponse($tenderFieldType->toArray(), trans('custom.tenderfieldtype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderFieldTypes/{id}",
     *      summary="Remove the specified TenderFieldType from storage",
     *      tags={"TenderFieldType"},
     *      description="Delete TenderFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderFieldType",
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
        /** @var TenderFieldType $tenderFieldType */
        $tenderFieldType = $this->tenderFieldTypeRepository->findWithoutFail($id);

        if (empty($tenderFieldType)) {
            return $this->sendError(trans('custom.tender_field_type_not_found'));
        }

        $tenderFieldType->delete();

        return $this->sendSuccess('Tender Field Type deleted successfully');
    }
}
