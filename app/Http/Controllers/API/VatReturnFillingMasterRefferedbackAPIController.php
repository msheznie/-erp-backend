<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFillingMasterRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingMasterRefferedbackAPIRequest;
use App\Models\VatReturnFillingMasterRefferedback;
use App\Repositories\VatReturnFillingMasterRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFillingMasterRefferedbackController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingMasterRefferedbackAPIController extends AppBaseController
{
    /** @var  VatReturnFillingMasterRefferedbackRepository */
    private $vatReturnFillingMasterRefferedbackRepository;

    public function __construct(VatReturnFillingMasterRefferedbackRepository $vatReturnFillingMasterRefferedbackRepo)
    {
        $this->vatReturnFillingMasterRefferedbackRepository = $vatReturnFillingMasterRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingMasterRefferedbacks",
     *      summary="Get a listing of the VatReturnFillingMasterRefferedbacks.",
     *      tags={"VatReturnFillingMasterRefferedback"},
     *      description="Get all VatReturnFillingMasterRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFillingMasterRefferedback")
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
        $this->vatReturnFillingMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingMasterRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingMasterRefferedbacks = $this->vatReturnFillingMasterRefferedbackRepository->all();

        return $this->sendResponse($vatReturnFillingMasterRefferedbacks->toArray(), 'Vat Return Filling Master Refferedbacks retrieved successfully');
    }

    /**
     * @param CreateVatReturnFillingMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFillingMasterRefferedbacks",
     *      summary="Store a newly created VatReturnFillingMasterRefferedback in storage",
     *      tags={"VatReturnFillingMasterRefferedback"},
     *      description="Store VatReturnFillingMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingMasterRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingMasterRefferedback")
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
     *                  ref="#/definitions/VatReturnFillingMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFillingMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepository->create($input);

        return $this->sendResponse($vatReturnFillingMasterRefferedback->toArray(), 'Vat Return Filling Master Refferedback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingMasterRefferedbacks/{id}",
     *      summary="Display the specified VatReturnFillingMasterRefferedback",
     *      tags={"VatReturnFillingMasterRefferedback"},
     *      description="Get VatReturnFillingMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMasterRefferedback",
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
     *                  ref="#/definitions/VatReturnFillingMasterRefferedback"
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
        /** @var VatReturnFillingMasterRefferedback $vatReturnFillingMasterRefferedback */
        $vatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMasterRefferedback)) {
            return $this->sendError('Vat Return Filling Master Refferedback not found');
        }

        return $this->sendResponse($vatReturnFillingMasterRefferedback->toArray(), 'Vat Return Filling Master Refferedback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFillingMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFillingMasterRefferedbacks/{id}",
     *      summary="Update the specified VatReturnFillingMasterRefferedback in storage",
     *      tags={"VatReturnFillingMasterRefferedback"},
     *      description="Update VatReturnFillingMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMasterRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingMasterRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingMasterRefferedback")
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
     *                  ref="#/definitions/VatReturnFillingMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingMasterRefferedback $vatReturnFillingMasterRefferedback */
        $vatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMasterRefferedback)) {
            return $this->sendError('Vat Return Filling Master Refferedback not found');
        }

        $vatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepository->update($input, $id);

        return $this->sendResponse($vatReturnFillingMasterRefferedback->toArray(), 'VatReturnFillingMasterRefferedback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFillingMasterRefferedbacks/{id}",
     *      summary="Remove the specified VatReturnFillingMasterRefferedback from storage",
     *      tags={"VatReturnFillingMasterRefferedback"},
     *      description="Delete VatReturnFillingMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMasterRefferedback",
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
        /** @var VatReturnFillingMasterRefferedback $vatReturnFillingMasterRefferedback */
        $vatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMasterRefferedback)) {
            return $this->sendError('Vat Return Filling Master Refferedback not found');
        }

        $vatReturnFillingMasterRefferedback->delete();

        return $this->sendSuccess('Vat Return Filling Master Refferedback deleted successfully');
    }
}
