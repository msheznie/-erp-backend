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

        return $this->sendResponse($vatReturnFilledCategoryRefferedbacks->toArray(), 'Vat Return Filled Category Refferedbacks retrieved successfully');
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

        return $this->sendResponse($vatReturnFilledCategoryRefferedback->toArray(), 'Vat Return Filled Category Refferedback saved successfully');
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
            return $this->sendError('Vat Return Filled Category Refferedback not found');
        }

        return $this->sendResponse($vatReturnFilledCategoryRefferedback->toArray(), 'Vat Return Filled Category Refferedback retrieved successfully');
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
            return $this->sendError('Vat Return Filled Category Refferedback not found');
        }

        $vatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepository->update($input, $id);

        return $this->sendResponse($vatReturnFilledCategoryRefferedback->toArray(), 'VatReturnFilledCategoryRefferedback updated successfully');
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
            return $this->sendError('Vat Return Filled Category Refferedback not found');
        }

        $vatReturnFilledCategoryRefferedback->delete();

        return $this->sendSuccess('Vat Return Filled Category Refferedback deleted successfully');
    }
}
