<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSDepartmentMasterAPIRequest;
use App\Http\Requests\API\UpdateHRMSDepartmentMasterAPIRequest;
use App\Models\HRMSDepartmentMaster;
use App\Repositories\HRMSDepartmentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSDepartmentMasterController
 * @package App\Http\Controllers\API
 */

class HRMSDepartmentMasterAPIController extends AppBaseController
{
    /** @var  HRMSDepartmentMasterRepository */
    private $hRMSDepartmentMasterRepository;

    public function __construct(HRMSDepartmentMasterRepository $hRMSDepartmentMasterRepo)
    {
        $this->hRMSDepartmentMasterRepository = $hRMSDepartmentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSDepartmentMasters",
     *      summary="Get a listing of the HRMSDepartmentMasters.",
     *      tags={"HRMSDepartmentMaster"},
     *      description="Get all HRMSDepartmentMasters",
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
     *                  @SWG\Items(ref="#/definitions/HRMSDepartmentMaster")
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
        $this->hRMSDepartmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSDepartmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSDepartmentMasters = $this->hRMSDepartmentMasterRepository->all();

        return $this->sendResponse($hRMSDepartmentMasters->toArray(), 'H R M S Department Masters retrieved successfully');
    }

    /**
     * @param CreateHRMSDepartmentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSDepartmentMasters",
     *      summary="Store a newly created HRMSDepartmentMaster in storage",
     *      tags={"HRMSDepartmentMaster"},
     *      description="Store HRMSDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSDepartmentMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSDepartmentMaster")
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
     *                  ref="#/definitions/HRMSDepartmentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        $hRMSDepartmentMasters = $this->hRMSDepartmentMasterRepository->create($input);

        return $this->sendResponse($hRMSDepartmentMasters->toArray(), 'H R M S Department Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSDepartmentMasters/{id}",
     *      summary="Display the specified HRMSDepartmentMaster",
     *      tags={"HRMSDepartmentMaster"},
     *      description="Get HRMSDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSDepartmentMaster",
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
     *                  ref="#/definitions/HRMSDepartmentMaster"
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
        /** @var HRMSDepartmentMaster $hRMSDepartmentMaster */
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            return $this->sendError('H R M S Department Master not found');
        }

        return $this->sendResponse($hRMSDepartmentMaster->toArray(), 'H R M S Department Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateHRMSDepartmentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSDepartmentMasters/{id}",
     *      summary="Update the specified HRMSDepartmentMaster in storage",
     *      tags={"HRMSDepartmentMaster"},
     *      description="Update HRMSDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSDepartmentMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSDepartmentMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSDepartmentMaster")
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
     *                  ref="#/definitions/HRMSDepartmentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSDepartmentMaster $hRMSDepartmentMaster */
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            return $this->sendError('H R M S Department Master not found');
        }

        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->update($input, $id);

        return $this->sendResponse($hRMSDepartmentMaster->toArray(), 'HRMSDepartmentMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSDepartmentMasters/{id}",
     *      summary="Remove the specified HRMSDepartmentMaster from storage",
     *      tags={"HRMSDepartmentMaster"},
     *      description="Delete HRMSDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSDepartmentMaster",
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
        /** @var HRMSDepartmentMaster $hRMSDepartmentMaster */
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            return $this->sendError('H R M S Department Master not found');
        }

        $hRMSDepartmentMaster->delete();

        return $this->sendResponse($id, 'H R M S Department Master deleted successfully');
    }
}
