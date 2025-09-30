<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFillingDetailsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingDetailsRefferedbackAPIRequest;
use App\Models\VatReturnFillingDetailsRefferedback;
use App\Repositories\VatReturnFillingDetailsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFillingDetailsRefferedbackController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingDetailsRefferedbackAPIController extends AppBaseController
{
    /** @var  VatReturnFillingDetailsRefferedbackRepository */
    private $vatReturnFillingDetailsRefferedbackRepository;

    public function __construct(VatReturnFillingDetailsRefferedbackRepository $vatReturnFillingDetailsRefferedbackRepo)
    {
        $this->vatReturnFillingDetailsRefferedbackRepository = $vatReturnFillingDetailsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingDetailsRefferedbacks",
     *      summary="Get a listing of the VatReturnFillingDetailsRefferedbacks.",
     *      tags={"VatReturnFillingDetailsRefferedback"},
     *      description="Get all VatReturnFillingDetailsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFillingDetailsRefferedback")
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
        $this->vatReturnFillingDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingDetailsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingDetailsRefferedbacks = $this->vatReturnFillingDetailsRefferedbackRepository->all();

        return $this->sendResponse($vatReturnFillingDetailsRefferedbacks->toArray(), trans('custom.vat_return_filling_details_refferedbacks_retrieved'));
    }

    /**
     * @param CreateVatReturnFillingDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFillingDetailsRefferedbacks",
     *      summary="Store a newly created VatReturnFillingDetailsRefferedback in storage",
     *      tags={"VatReturnFillingDetailsRefferedback"},
     *      description="Store VatReturnFillingDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingDetailsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingDetailsRefferedback")
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
     *                  ref="#/definitions/VatReturnFillingDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFillingDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepository->create($input);

        return $this->sendResponse($vatReturnFillingDetailsRefferedback->toArray(), trans('custom.vat_return_filling_details_refferedback_saved_succ'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingDetailsRefferedbacks/{id}",
     *      summary="Display the specified VatReturnFillingDetailsRefferedback",
     *      tags={"VatReturnFillingDetailsRefferedback"},
     *      description="Get VatReturnFillingDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingDetailsRefferedback",
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
     *                  ref="#/definitions/VatReturnFillingDetailsRefferedback"
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
        /** @var VatReturnFillingDetailsRefferedback $vatReturnFillingDetailsRefferedback */
        $vatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFillingDetailsRefferedback)) {
            return $this->sendError(trans('custom.vat_return_filling_details_refferedback_not_found'));
        }

        return $this->sendResponse($vatReturnFillingDetailsRefferedback->toArray(), trans('custom.vat_return_filling_details_refferedback_retrieved_'));
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFillingDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFillingDetailsRefferedbacks/{id}",
     *      summary="Update the specified VatReturnFillingDetailsRefferedback in storage",
     *      tags={"VatReturnFillingDetailsRefferedback"},
     *      description="Update VatReturnFillingDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingDetailsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingDetailsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingDetailsRefferedback")
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
     *                  ref="#/definitions/VatReturnFillingDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingDetailsRefferedback $vatReturnFillingDetailsRefferedback */
        $vatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFillingDetailsRefferedback)) {
            return $this->sendError(trans('custom.vat_return_filling_details_refferedback_not_found'));
        }

        $vatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($vatReturnFillingDetailsRefferedback->toArray(), trans('custom.vatreturnfillingdetailsrefferedback_updated_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFillingDetailsRefferedbacks/{id}",
     *      summary="Remove the specified VatReturnFillingDetailsRefferedback from storage",
     *      tags={"VatReturnFillingDetailsRefferedback"},
     *      description="Delete VatReturnFillingDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingDetailsRefferedback",
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
        /** @var VatReturnFillingDetailsRefferedback $vatReturnFillingDetailsRefferedback */
        $vatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFillingDetailsRefferedback)) {
            return $this->sendError(trans('custom.vat_return_filling_details_refferedback_not_found'));
        }

        $vatReturnFillingDetailsRefferedback->delete();

        return $this->sendSuccess('Vat Return Filling Details Refferedback deleted successfully');
    }
}
