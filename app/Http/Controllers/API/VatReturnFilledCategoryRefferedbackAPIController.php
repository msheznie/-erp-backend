<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFilledCategoryRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFilledCategoryRefferedbackAPIRequest;
use App\Models\VatReturnFilledCategoryRefferedback;
use App\Repositories\VatReturnFilledCategoryRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFilledCategoryRefferedbackController
 * @package App\Http\Controllers\API
 */

class VatReturnFilledCategoryRefferedbackAPIController extends AppBaseController
{
    /** @var  VatReturnFilledCategoryRefferedbackRepository */
    private $vatReturnFilledCategoryRefferedbackRepository;

    public function __construct(VatReturnFilledCategoryRefferedbackRepository $vatReturnFilledCategoryRefferedbackRepo)
    {
        $this->vatReturnFilledCategoryRefferedbackRepository = $vatReturnFilledCategoryRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFilledCategoryRefferedbacks",
     *      summary="Get a listing of the VatReturnFilledCategoryRefferedbacks.",
     *      tags={"VatReturnFilledCategoryRefferedback"},
     *      description="Get all VatReturnFilledCategoryRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFilledCategoryRefferedback")
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
        $this->vatReturnFilledCategoryRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFilledCategoryRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFilledCategoryRefferedbacks = $this->vatReturnFilledCategoryRefferedbackRepository->all();

        return $this->sendResponse($vatReturnFilledCategoryRefferedbacks->toArray(), trans('custom.vat_return_filled_category_refferedbacks_retrieved'));
    }

    /**
     * @param CreateVatReturnFilledCategoryRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFilledCategoryRefferedbacks",
     *      summary="Store a newly created VatReturnFilledCategoryRefferedback in storage",
     *      tags={"VatReturnFilledCategoryRefferedback"},
     *      description="Store VatReturnFilledCategoryRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFilledCategoryRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFilledCategoryRefferedback")
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
     *                  ref="#/definitions/VatReturnFilledCategoryRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFilledCategoryRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepository->create($input);

        return $this->sendResponse($vatReturnFilledCategoryRefferedback->toArray(), trans('custom.vat_return_filled_category_refferedback_saved_succ'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFilledCategoryRefferedbacks/{id}",
     *      summary="Display the specified VatReturnFilledCategoryRefferedback",
     *      tags={"VatReturnFilledCategoryRefferedback"},
     *      description="Get VatReturnFilledCategoryRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFilledCategoryRefferedback",
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
     *                  ref="#/definitions/VatReturnFilledCategoryRefferedback"
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
        /** @var VatReturnFilledCategoryRefferedback $vatReturnFilledCategoryRefferedback */
        $vatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFilledCategoryRefferedback)) {
            return $this->sendError(trans('custom.vat_return_filled_category_refferedback_not_found'));
        }

        return $this->sendResponse($vatReturnFilledCategoryRefferedback->toArray(), trans('custom.vat_return_filled_category_refferedback_retrieved_'));
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFilledCategoryRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFilledCategoryRefferedbacks/{id}",
     *      summary="Update the specified VatReturnFilledCategoryRefferedback in storage",
     *      tags={"VatReturnFilledCategoryRefferedback"},
     *      description="Update VatReturnFilledCategoryRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFilledCategoryRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFilledCategoryRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFilledCategoryRefferedback")
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
     *                  ref="#/definitions/VatReturnFilledCategoryRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFilledCategoryRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFilledCategoryRefferedback $vatReturnFilledCategoryRefferedback */
        $vatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFilledCategoryRefferedback)) {
            return $this->sendError(trans('custom.vat_return_filled_category_refferedback_not_found'));
        }

        $vatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepository->update($input, $id);

        return $this->sendResponse($vatReturnFilledCategoryRefferedback->toArray(), trans('custom.vatreturnfilledcategoryrefferedback_updated_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFilledCategoryRefferedbacks/{id}",
     *      summary="Remove the specified VatReturnFilledCategoryRefferedback from storage",
     *      tags={"VatReturnFilledCategoryRefferedback"},
     *      description="Delete VatReturnFilledCategoryRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFilledCategoryRefferedback",
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
        /** @var VatReturnFilledCategoryRefferedback $vatReturnFilledCategoryRefferedback */
        $vatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFilledCategoryRefferedback)) {
            return $this->sendError(trans('custom.vat_return_filled_category_refferedback_not_found'));
        }

        $vatReturnFilledCategoryRefferedback->delete();

        return $this->sendSuccess('Vat Return Filled Category Refferedback deleted successfully');
    }
}
