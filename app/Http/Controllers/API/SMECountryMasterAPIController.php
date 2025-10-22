<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMECountryMasterAPIRequest;
use App\Http\Requests\API\UpdateSMECountryMasterAPIRequest;
use App\Models\SMECountryMaster;
use App\Repositories\SMECountryMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMECountryMasterController
 * @package App\Http\Controllers\API
 */

class SMECountryMasterAPIController extends AppBaseController
{
    /** @var  SMECountryMasterRepository */
    private $sMECountryMasterRepository;

    public function __construct(SMECountryMasterRepository $sMECountryMasterRepo)
    {
        $this->sMECountryMasterRepository = $sMECountryMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECountryMasters",
     *      summary="Get a listing of the SMECountryMasters.",
     *      tags={"SMECountryMaster"},
     *      description="Get all SMECountryMasters",
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
     *                  @SWG\Items(ref="#/definitions/SMECountryMaster")
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
        $this->sMECountryMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sMECountryMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMECountryMasters = $this->sMECountryMasterRepository->all();

        return $this->sendResponse($sMECountryMasters->toArray(), trans('custom.s_m_e_country_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSMECountryMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMECountryMasters",
     *      summary="Store a newly created SMECountryMaster in storage",
     *      tags={"SMECountryMaster"},
     *      description="Store SMECountryMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECountryMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECountryMaster")
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
     *                  ref="#/definitions/SMECountryMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMECountryMasterAPIRequest $request)
    {
        $input = $request->all();

        $sMECountryMaster = $this->sMECountryMasterRepository->create($input);

        return $this->sendResponse($sMECountryMaster->toArray(), trans('custom.s_m_e_country_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECountryMasters/{id}",
     *      summary="Display the specified SMECountryMaster",
     *      tags={"SMECountryMaster"},
     *      description="Get SMECountryMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECountryMaster",
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
     *                  ref="#/definitions/SMECountryMaster"
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
        /** @var SMECountryMaster $sMECountryMaster */
        $sMECountryMaster = $this->sMECountryMasterRepository->findWithoutFail($id);

        if (empty($sMECountryMaster)) {
            return $this->sendError(trans('custom.s_m_e_country_master_not_found'));
        }

        return $this->sendResponse($sMECountryMaster->toArray(), trans('custom.s_m_e_country_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMECountryMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMECountryMasters/{id}",
     *      summary="Update the specified SMECountryMaster in storage",
     *      tags={"SMECountryMaster"},
     *      description="Update SMECountryMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECountryMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECountryMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECountryMaster")
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
     *                  ref="#/definitions/SMECountryMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMECountryMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMECountryMaster $sMECountryMaster */
        $sMECountryMaster = $this->sMECountryMasterRepository->findWithoutFail($id);

        if (empty($sMECountryMaster)) {
            return $this->sendError(trans('custom.s_m_e_country_master_not_found'));
        }

        $sMECountryMaster = $this->sMECountryMasterRepository->update($input, $id);

        return $this->sendResponse($sMECountryMaster->toArray(), trans('custom.smecountrymaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMECountryMasters/{id}",
     *      summary="Remove the specified SMECountryMaster from storage",
     *      tags={"SMECountryMaster"},
     *      description="Delete SMECountryMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECountryMaster",
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
        /** @var SMECountryMaster $sMECountryMaster */
        $sMECountryMaster = $this->sMECountryMasterRepository->findWithoutFail($id);

        if (empty($sMECountryMaster)) {
            return $this->sendError(trans('custom.s_m_e_country_master_not_found'));
        }

        $sMECountryMaster->delete();

        return $this->sendSuccess('S M E Country Master deleted successfully');
    }
}
