<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInsurancePolicyTypeAPIRequest;
use App\Http\Requests\API\UpdateInsurancePolicyTypeAPIRequest;
use App\Models\InsurancePolicyType;
use App\Repositories\InsurancePolicyTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InsurancePolicyTypeController
 * @package App\Http\Controllers\API
 */

class InsurancePolicyTypeAPIController extends AppBaseController
{
    /** @var  InsurancePolicyTypeRepository */
    private $insurancePolicyTypeRepository;

    public function __construct(InsurancePolicyTypeRepository $insurancePolicyTypeRepo)
    {
        $this->insurancePolicyTypeRepository = $insurancePolicyTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/insurancePolicyTypes",
     *      summary="Get a listing of the InsurancePolicyTypes.",
     *      tags={"InsurancePolicyType"},
     *      description="Get all InsurancePolicyTypes",
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
     *                  @SWG\Items(ref="#/definitions/InsurancePolicyType")
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
        $this->insurancePolicyTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->insurancePolicyTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $insurancePolicyTypes = $this->insurancePolicyTypeRepository->all();

        return $this->sendResponse($insurancePolicyTypes->toArray(), trans('custom.insurance_policy_types_retrieved_successfully'));
    }

    /**
     * @param CreateInsurancePolicyTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/insurancePolicyTypes",
     *      summary="Store a newly created InsurancePolicyType in storage",
     *      tags={"InsurancePolicyType"},
     *      description="Store InsurancePolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InsurancePolicyType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InsurancePolicyType")
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
     *                  ref="#/definitions/InsurancePolicyType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInsurancePolicyTypeAPIRequest $request)
    {
        $input = $request->all();

        $insurancePolicyTypes = $this->insurancePolicyTypeRepository->create($input);

        return $this->sendResponse($insurancePolicyTypes->toArray(), trans('custom.insurance_policy_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/insurancePolicyTypes/{id}",
     *      summary="Display the specified InsurancePolicyType",
     *      tags={"InsurancePolicyType"},
     *      description="Get InsurancePolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InsurancePolicyType",
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
     *                  ref="#/definitions/InsurancePolicyType"
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
        /** @var InsurancePolicyType $insurancePolicyType */
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            return $this->sendError(trans('custom.insurance_policy_type_not_found'));
        }

        return $this->sendResponse($insurancePolicyType->toArray(), trans('custom.insurance_policy_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateInsurancePolicyTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/insurancePolicyTypes/{id}",
     *      summary="Update the specified InsurancePolicyType in storage",
     *      tags={"InsurancePolicyType"},
     *      description="Update InsurancePolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InsurancePolicyType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InsurancePolicyType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InsurancePolicyType")
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
     *                  ref="#/definitions/InsurancePolicyType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInsurancePolicyTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var InsurancePolicyType $insurancePolicyType */
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            return $this->sendError(trans('custom.insurance_policy_type_not_found'));
        }

        $insurancePolicyType = $this->insurancePolicyTypeRepository->update($input, $id);

        return $this->sendResponse($insurancePolicyType->toArray(), trans('custom.insurancepolicytype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/insurancePolicyTypes/{id}",
     *      summary="Remove the specified InsurancePolicyType from storage",
     *      tags={"InsurancePolicyType"},
     *      description="Delete InsurancePolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InsurancePolicyType",
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
        /** @var InsurancePolicyType $insurancePolicyType */
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            return $this->sendError(trans('custom.insurance_policy_type_not_found'));
        }

        $insurancePolicyType->delete();

        return $this->sendResponse($id, trans('custom.insurance_policy_type_deleted_successfully'));
    }
}
