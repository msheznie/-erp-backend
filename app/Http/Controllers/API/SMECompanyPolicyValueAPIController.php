<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMECompanyPolicyValueAPIRequest;
use App\Http\Requests\API\UpdateSMECompanyPolicyValueAPIRequest;
use App\Models\SMECompanyPolicyValue;
use App\Repositories\SMECompanyPolicyValueRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMECompanyPolicyValueController
 * @package App\Http\Controllers\API
 */

class SMECompanyPolicyValueAPIController extends AppBaseController
{
    /** @var  SMECompanyPolicyValueRepository */
    private $sMECompanyPolicyValueRepository;

    public function __construct(SMECompanyPolicyValueRepository $sMECompanyPolicyValueRepo)
    {
        $this->sMECompanyPolicyValueRepository = $sMECompanyPolicyValueRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECompanyPolicyValues",
     *      summary="Get a listing of the SMECompanyPolicyValues.",
     *      tags={"SMECompanyPolicyValue"},
     *      description="Get all SMECompanyPolicyValues",
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
     *                  @SWG\Items(ref="#/definitions/SMECompanyPolicyValue")
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
        $this->sMECompanyPolicyValueRepository->pushCriteria(new RequestCriteria($request));
        $this->sMECompanyPolicyValueRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMECompanyPolicyValues = $this->sMECompanyPolicyValueRepository->all();

        return $this->sendResponse($sMECompanyPolicyValues->toArray(), trans('custom.s_m_e_company_policy_values_retrieved_successfully'));
    }

    /**
     * @param CreateSMECompanyPolicyValueAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMECompanyPolicyValues",
     *      summary="Store a newly created SMECompanyPolicyValue in storage",
     *      tags={"SMECompanyPolicyValue"},
     *      description="Store SMECompanyPolicyValue",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECompanyPolicyValue that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECompanyPolicyValue")
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
     *                  ref="#/definitions/SMECompanyPolicyValue"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMECompanyPolicyValueAPIRequest $request)
    {
        $input = $request->all();

        $sMECompanyPolicyValue = $this->sMECompanyPolicyValueRepository->create($input);

        return $this->sendResponse($sMECompanyPolicyValue->toArray(), trans('custom.s_m_e_company_policy_value_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECompanyPolicyValues/{id}",
     *      summary="Display the specified SMECompanyPolicyValue",
     *      tags={"SMECompanyPolicyValue"},
     *      description="Get SMECompanyPolicyValue",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicyValue",
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
     *                  ref="#/definitions/SMECompanyPolicyValue"
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
        /** @var SMECompanyPolicyValue $sMECompanyPolicyValue */
        $sMECompanyPolicyValue = $this->sMECompanyPolicyValueRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicyValue)) {
            return $this->sendError(trans('custom.s_m_e_company_policy_value_not_found'));
        }

        return $this->sendResponse($sMECompanyPolicyValue->toArray(), trans('custom.s_m_e_company_policy_value_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMECompanyPolicyValueAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMECompanyPolicyValues/{id}",
     *      summary="Update the specified SMECompanyPolicyValue in storage",
     *      tags={"SMECompanyPolicyValue"},
     *      description="Update SMECompanyPolicyValue",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicyValue",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECompanyPolicyValue that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECompanyPolicyValue")
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
     *                  ref="#/definitions/SMECompanyPolicyValue"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMECompanyPolicyValueAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMECompanyPolicyValue $sMECompanyPolicyValue */
        $sMECompanyPolicyValue = $this->sMECompanyPolicyValueRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicyValue)) {
            return $this->sendError(trans('custom.s_m_e_company_policy_value_not_found'));
        }

        $sMECompanyPolicyValue = $this->sMECompanyPolicyValueRepository->update($input, $id);

        return $this->sendResponse($sMECompanyPolicyValue->toArray(), trans('custom.smecompanypolicyvalue_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMECompanyPolicyValues/{id}",
     *      summary="Remove the specified SMECompanyPolicyValue from storage",
     *      tags={"SMECompanyPolicyValue"},
     *      description="Delete SMECompanyPolicyValue",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicyValue",
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
        /** @var SMECompanyPolicyValue $sMECompanyPolicyValue */
        $sMECompanyPolicyValue = $this->sMECompanyPolicyValueRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicyValue)) {
            return $this->sendError(trans('custom.s_m_e_company_policy_value_not_found'));
        }

        $sMECompanyPolicyValue->delete();

        return $this->sendSuccess('S M E Company Policy Value deleted successfully');
    }
}
