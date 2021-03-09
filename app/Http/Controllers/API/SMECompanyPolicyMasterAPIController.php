<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMECompanyPolicyMasterAPIRequest;
use App\Http\Requests\API\UpdateSMECompanyPolicyMasterAPIRequest;
use App\Models\SMECompanyPolicyMaster;
use App\Repositories\SMECompanyPolicyMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMECompanyPolicyMasterController
 * @package App\Http\Controllers\API
 */

class SMECompanyPolicyMasterAPIController extends AppBaseController
{
    /** @var  SMECompanyPolicyMasterRepository */
    private $sMECompanyPolicyMasterRepository;

    public function __construct(SMECompanyPolicyMasterRepository $sMECompanyPolicyMasterRepo)
    {
        $this->sMECompanyPolicyMasterRepository = $sMECompanyPolicyMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECompanyPolicyMasters",
     *      summary="Get a listing of the SMECompanyPolicyMasters.",
     *      tags={"SMECompanyPolicyMaster"},
     *      description="Get all SMECompanyPolicyMasters",
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
     *                  @SWG\Items(ref="#/definitions/SMECompanyPolicyMaster")
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
        $this->sMECompanyPolicyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sMECompanyPolicyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMECompanyPolicyMasters = $this->sMECompanyPolicyMasterRepository->all();

        return $this->sendResponse($sMECompanyPolicyMasters->toArray(), 'S M E Company Policy Masters retrieved successfully');
    }

    /**
     * @param CreateSMECompanyPolicyMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMECompanyPolicyMasters",
     *      summary="Store a newly created SMECompanyPolicyMaster in storage",
     *      tags={"SMECompanyPolicyMaster"},
     *      description="Store SMECompanyPolicyMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECompanyPolicyMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECompanyPolicyMaster")
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
     *                  ref="#/definitions/SMECompanyPolicyMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMECompanyPolicyMasterAPIRequest $request)
    {
        $input = $request->all();

        $sMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepository->create($input);

        return $this->sendResponse($sMECompanyPolicyMaster->toArray(), 'S M E Company Policy Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECompanyPolicyMasters/{id}",
     *      summary="Display the specified SMECompanyPolicyMaster",
     *      tags={"SMECompanyPolicyMaster"},
     *      description="Get SMECompanyPolicyMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicyMaster",
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
     *                  ref="#/definitions/SMECompanyPolicyMaster"
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
        /** @var SMECompanyPolicyMaster $sMECompanyPolicyMaster */
        $sMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicyMaster)) {
            return $this->sendError('S M E Company Policy Master not found');
        }

        return $this->sendResponse($sMECompanyPolicyMaster->toArray(), 'S M E Company Policy Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSMECompanyPolicyMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMECompanyPolicyMasters/{id}",
     *      summary="Update the specified SMECompanyPolicyMaster in storage",
     *      tags={"SMECompanyPolicyMaster"},
     *      description="Update SMECompanyPolicyMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicyMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECompanyPolicyMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECompanyPolicyMaster")
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
     *                  ref="#/definitions/SMECompanyPolicyMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMECompanyPolicyMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMECompanyPolicyMaster $sMECompanyPolicyMaster */
        $sMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicyMaster)) {
            return $this->sendError('S M E Company Policy Master not found');
        }

        $sMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepository->update($input, $id);

        return $this->sendResponse($sMECompanyPolicyMaster->toArray(), 'SMECompanyPolicyMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMECompanyPolicyMasters/{id}",
     *      summary="Remove the specified SMECompanyPolicyMaster from storage",
     *      tags={"SMECompanyPolicyMaster"},
     *      description="Delete SMECompanyPolicyMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicyMaster",
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
        /** @var SMECompanyPolicyMaster $sMECompanyPolicyMaster */
        $sMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicyMaster)) {
            return $this->sendError('S M E Company Policy Master not found');
        }

        $sMECompanyPolicyMaster->delete();

        return $this->sendSuccess('S M E Company Policy Master deleted successfully');
    }
}
