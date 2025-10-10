<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFillingDetailAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingDetailAPIRequest;
use App\Models\VatReturnFillingDetail;
use App\Repositories\VatReturnFillingDetailRepository;
use App\Repositories\VatReturnFillingMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFillingDetailController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingDetailAPIController extends AppBaseController
{
    /** @var  VatReturnFillingDetailRepository */
    private $vatReturnFillingDetailRepository;
    private $vatReturnFillingMasterRepository;

    public function __construct(VatReturnFillingDetailRepository $vatReturnFillingDetailRepo, VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->vatReturnFillingDetailRepository = $vatReturnFillingDetailRepo;
        $this->vatReturnFillingMasterRepository = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingDetails",
     *      summary="Get a listing of the VatReturnFillingDetails.",
     *      tags={"VatReturnFillingDetail"},
     *      description="Get all VatReturnFillingDetails",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFillingDetail")
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
        $this->vatReturnFillingDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingDetails = $this->vatReturnFillingDetailRepository->all();

        return $this->sendResponse($vatReturnFillingDetails->toArray(), trans('custom.vat_return_filling_details_retrieved_successfully'));
    }

    /**
     * @param CreateVatReturnFillingDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFillingDetails",
     *      summary="Store a newly created VatReturnFillingDetail in storage",
     *      tags={"VatReturnFillingDetail"},
     *      description="Store VatReturnFillingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingDetail")
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
     *                  ref="#/definitions/VatReturnFillingDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFillingDetailAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFillingDetail = $this->vatReturnFillingDetailRepository->create($input);

        return $this->sendResponse($vatReturnFillingDetail->toArray(), trans('custom.vat_return_filling_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingDetails/{id}",
     *      summary="Display the specified VatReturnFillingDetail",
     *      tags={"VatReturnFillingDetail"},
     *      description="Get VatReturnFillingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingDetail",
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
     *                  ref="#/definitions/VatReturnFillingDetail"
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
        /** @var VatReturnFillingDetail $vatReturnFillingDetail */
        $vatReturnFillingDetail = $this->vatReturnFillingDetailRepository->findWithoutFail($id);

        if (empty($vatReturnFillingDetail)) {
            return $this->sendError(trans('custom.vat_return_filling_detail_not_found'));
        }

        return $this->sendResponse($vatReturnFillingDetail->toArray(), trans('custom.vat_return_filling_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFillingDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFillingDetails/{id}",
     *      summary="Update the specified VatReturnFillingDetail in storage",
     *      tags={"VatReturnFillingDetail"},
     *      description="Update VatReturnFillingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingDetail")
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
     *                  ref="#/definitions/VatReturnFillingDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingDetail $vatReturnFillingDetail */
        $vatReturnFillingDetail = $this->vatReturnFillingDetailRepository->findWithoutFail($id);

        if (empty($vatReturnFillingDetail)) {
            return $this->sendError(trans('custom.vat_return_filling_detail_not_found'));
        }

        if (isset($input['category'])) {
            unset($input['category']);
        }

        $vatReturnFillingDetail = $this->vatReturnFillingDetailRepository->update($input, $id);

        $this->vatReturnFillingMasterRepository->updateFillingFormula($vatReturnFillingDetail->vatReturnFillingID);

        return $this->sendResponse($vatReturnFillingDetail->toArray(), trans('custom.vatreturnfillingdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFillingDetails/{id}",
     *      summary="Remove the specified VatReturnFillingDetail from storage",
     *      tags={"VatReturnFillingDetail"},
     *      description="Delete VatReturnFillingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingDetail",
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
        /** @var VatReturnFillingDetail $vatReturnFillingDetail */
        $vatReturnFillingDetail = $this->vatReturnFillingDetailRepository->findWithoutFail($id);

        if (empty($vatReturnFillingDetail)) {
            return $this->sendError(trans('custom.vat_return_filling_detail_not_found'));
        }

        $vatReturnFillingDetail->delete();

        return $this->sendSuccess('Vat Return Filling Detail deleted successfully');
    }
}
