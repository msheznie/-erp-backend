<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMECompanyPolicyAPIRequest;
use App\Http\Requests\API\UpdateSMECompanyPolicyAPIRequest;
use App\Models\SMECompanyPolicy;
use App\Repositories\SMECompanyPolicyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMECompanyPolicyController
 * @package App\Http\Controllers\API
 */

class SMECompanyPolicyAPIController extends AppBaseController
{
    /** @var  SMECompanyPolicyRepository */
    private $sMECompanyPolicyRepository;

    public function __construct(SMECompanyPolicyRepository $sMECompanyPolicyRepo)
    {
        $this->sMECompanyPolicyRepository = $sMECompanyPolicyRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECompanyPolicies",
     *      summary="Get a listing of the SMECompanyPolicies.",
     *      tags={"SMECompanyPolicy"},
     *      description="Get all SMECompanyPolicies",
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
     *                  @SWG\Items(ref="#/definitions/SMECompanyPolicy")
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
        $this->sMECompanyPolicyRepository->pushCriteria(new RequestCriteria($request));
        $this->sMECompanyPolicyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMECompanyPolicies = $this->sMECompanyPolicyRepository->all();

        return $this->sendResponse($sMECompanyPolicies->toArray(), trans('custom.s_m_e_company_policies_retrieved_successfully'));
    }

    /**
     * @param CreateSMECompanyPolicyAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMECompanyPolicies",
     *      summary="Store a newly created SMECompanyPolicy in storage",
     *      tags={"SMECompanyPolicy"},
     *      description="Store SMECompanyPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECompanyPolicy that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECompanyPolicy")
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
     *                  ref="#/definitions/SMECompanyPolicy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMECompanyPolicyAPIRequest $request)
    {
        $input = $request->all();

        $sMECompanyPolicy = $this->sMECompanyPolicyRepository->create($input);

        return $this->sendResponse($sMECompanyPolicy->toArray(), trans('custom.s_m_e_company_policy_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECompanyPolicies/{id}",
     *      summary="Display the specified SMECompanyPolicy",
     *      tags={"SMECompanyPolicy"},
     *      description="Get SMECompanyPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicy",
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
     *                  ref="#/definitions/SMECompanyPolicy"
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
        /** @var SMECompanyPolicy $sMECompanyPolicy */
        $sMECompanyPolicy = $this->sMECompanyPolicyRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicy)) {
            return $this->sendError(trans('custom.s_m_e_company_policy_not_found'));
        }

        return $this->sendResponse($sMECompanyPolicy->toArray(), trans('custom.s_m_e_company_policy_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMECompanyPolicyAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMECompanyPolicies/{id}",
     *      summary="Update the specified SMECompanyPolicy in storage",
     *      tags={"SMECompanyPolicy"},
     *      description="Update SMECompanyPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicy",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECompanyPolicy that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECompanyPolicy")
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
     *                  ref="#/definitions/SMECompanyPolicy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMECompanyPolicyAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMECompanyPolicy $sMECompanyPolicy */
        $sMECompanyPolicy = $this->sMECompanyPolicyRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicy)) {
            return $this->sendError(trans('custom.s_m_e_company_policy_not_found'));
        }

        $sMECompanyPolicy = $this->sMECompanyPolicyRepository->update($input, $id);

        return $this->sendResponse($sMECompanyPolicy->toArray(), trans('custom.smecompanypolicy_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMECompanyPolicies/{id}",
     *      summary="Remove the specified SMECompanyPolicy from storage",
     *      tags={"SMECompanyPolicy"},
     *      description="Delete SMECompanyPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECompanyPolicy",
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
        /** @var SMECompanyPolicy $sMECompanyPolicy */
        $sMECompanyPolicy = $this->sMECompanyPolicyRepository->findWithoutFail($id);

        if (empty($sMECompanyPolicy)) {
            return $this->sendError(trans('custom.s_m_e_company_policy_not_found'));
        }

        $sMECompanyPolicy->delete();

        return $this->sendSuccess('S M E Company Policy deleted successfully');
    }
}
