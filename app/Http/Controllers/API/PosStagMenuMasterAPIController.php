<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePosStagMenuMasterAPIRequest;
use App\Http\Requests\API\UpdatePosStagMenuMasterAPIRequest;
use App\Models\PosStagMenuMaster;
use App\Repositories\PosStagMenuMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PosStagMenuMasterController
 * @package App\Http\Controllers\API
 */

class PosStagMenuMasterAPIController extends AppBaseController
{
    /** @var  PosStagMenuMasterRepository */
    private $posStagMenuMasterRepository;

    public function __construct(PosStagMenuMasterRepository $posStagMenuMasterRepo)
    {
        $this->posStagMenuMasterRepository = $posStagMenuMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/posStagMenuMasters",
     *      summary="Get a listing of the PosStagMenuMasters.",
     *      tags={"PosStagMenuMaster"},
     *      description="Get all PosStagMenuMasters",
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
     *                  @SWG\Items(ref="#/definitions/PosStagMenuMaster")
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
        $this->posStagMenuMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->posStagMenuMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $posStagMenuMasters = $this->posStagMenuMasterRepository->all();

        return $this->sendResponse($posStagMenuMasters->toArray(), 'Pos Stag Menu Masters retrieved successfully');
    }

    /**
     * @param CreatePosStagMenuMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posStagMenuMasters",
     *      summary="Store a newly created PosStagMenuMaster in storage",
     *      tags={"PosStagMenuMaster"},
     *      description="Store PosStagMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosStagMenuMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosStagMenuMaster")
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
     *                  ref="#/definitions/PosStagMenuMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePosStagMenuMasterAPIRequest $request)
    {
        $input = $request->all();

        $posStagMenuMaster = $this->posStagMenuMasterRepository->create($input);

        return $this->sendResponse($posStagMenuMaster->toArray(), 'Pos Stag Menu Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/posStagMenuMasters/{id}",
     *      summary="Display the specified PosStagMenuMaster",
     *      tags={"PosStagMenuMaster"},
     *      description="Get PosStagMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosStagMenuMaster",
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
     *                  ref="#/definitions/PosStagMenuMaster"
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
        /** @var PosStagMenuMaster $posStagMenuMaster */
        $posStagMenuMaster = $this->posStagMenuMasterRepository->findWithoutFail($id);

        if (empty($posStagMenuMaster)) {
            return $this->sendError('Pos Stag Menu Master not found');
        }

        return $this->sendResponse($posStagMenuMaster->toArray(), 'Pos Stag Menu Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePosStagMenuMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/posStagMenuMasters/{id}",
     *      summary="Update the specified PosStagMenuMaster in storage",
     *      tags={"PosStagMenuMaster"},
     *      description="Update PosStagMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosStagMenuMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosStagMenuMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosStagMenuMaster")
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
     *                  ref="#/definitions/PosStagMenuMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePosStagMenuMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PosStagMenuMaster $posStagMenuMaster */
        $posStagMenuMaster = $this->posStagMenuMasterRepository->findWithoutFail($id);

        if (empty($posStagMenuMaster)) {
            return $this->sendError('Pos Stag Menu Master not found');
        }

        $posStagMenuMaster = $this->posStagMenuMasterRepository->update($input, $id);

        return $this->sendResponse($posStagMenuMaster->toArray(), 'PosStagMenuMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posStagMenuMasters/{id}",
     *      summary="Remove the specified PosStagMenuMaster from storage",
     *      tags={"PosStagMenuMaster"},
     *      description="Delete PosStagMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosStagMenuMaster",
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
        /** @var PosStagMenuMaster $posStagMenuMaster */
        $posStagMenuMaster = $this->posStagMenuMasterRepository->findWithoutFail($id);

        if (empty($posStagMenuMaster)) {
            return $this->sendError('Pos Stag Menu Master not found');
        }

        $posStagMenuMaster->delete();

        return $this->sendSuccess('Pos Stag Menu Master deleted successfully');
    }
}
