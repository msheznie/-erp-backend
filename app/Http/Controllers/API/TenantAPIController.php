<?php

namespace App\Http\Controllers\API;

use App\helper\TaxService;
use App\Http\Requests\API\CreateTenantAPIRequest;
use App\Http\Requests\API\UpdateTenantAPIRequest;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseOrderDetails;
use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenantController
 * @package App\Http\Controllers\API
 */

class TenantAPIController extends AppBaseController
{
    /** @var  TenantRepository */
    private $tenantRepository;

    public function __construct(TenantRepository $tenantRepo)
    {
        $this->tenantRepository = $tenantRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenants",
     *      summary="Get a listing of the Tenants.",
     *      tags={"Tenant"},
     *      description="Get all Tenants",
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
     *                  @SWG\Items(ref="#/definitions/Tenant")
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
        $this->tenantRepository->pushCriteria(new RequestCriteria($request));
        $this->tenantRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenants = $this->tenantRepository->all();

        return $this->sendResponse($tenants->toArray(), trans('custom.tenants_retrieved_successfully'));
    }

    /**
     * @param CreateTenantAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenants",
     *      summary="Store a newly created Tenant in storage",
     *      tags={"Tenant"},
     *      description="Store Tenant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Tenant that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Tenant")
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
     *                  ref="#/definitions/Tenant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenantAPIRequest $request)
    {
        $input = $request->all();

        $tenant = $this->tenantRepository->create($input);

        return $this->sendResponse($tenant->toArray(), trans('custom.tenant_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenants/{id}",
     *      summary="Display the specified Tenant",
     *      tags={"Tenant"},
     *      description="Get Tenant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tenant",
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
     *                  ref="#/definitions/Tenant"
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
        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            return $this->sendError(trans('custom.tenant_not_found'));
        }

        return $this->sendResponse($tenant->toArray(), trans('custom.tenant_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenantAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenants/{id}",
     *      summary="Update the specified Tenant in storage",
     *      tags={"Tenant"},
     *      description="Update Tenant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tenant",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Tenant that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Tenant")
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
     *                  ref="#/definitions/Tenant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenantAPIRequest $request)
    {
        $input = $request->all();

        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            return $this->sendError(trans('custom.tenant_not_found'));
        }

        $tenant = $this->tenantRepository->update($input, $id);

        return $this->sendResponse($tenant->toArray(), trans('custom.tenant_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenants/{id}",
     *      summary="Remove the specified Tenant from storage",
     *      tags={"Tenant"},
     *      description="Delete Tenant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tenant",
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
        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            return $this->sendError(trans('custom.tenant_not_found'));
        }

        $tenant->delete();

        return $this->sendSuccess('Tenant deleted successfully');
    }


    public function test(Request $request)
    {

        $data = env('IS_MULTI_TENANCY');

        $output = TaxService::poLogisticVATDistributionForGRV(58732);

        return $this->sendResponse($output, trans('custom.retrieved_successfully_1') . $request->input('api_key'));
    }
}
