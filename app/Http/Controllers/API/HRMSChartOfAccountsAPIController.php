<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSChartOfAccountsAPIRequest;
use App\Http\Requests\API\UpdateHRMSChartOfAccountsAPIRequest;
use App\Models\HRMSChartOfAccounts;
use App\Repositories\HRMSChartOfAccountsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSChartOfAccountsController
 * @package App\Http\Controllers\API
 */

class HRMSChartOfAccountsAPIController extends AppBaseController
{
    /** @var  HRMSChartOfAccountsRepository */
    private $hRMSChartOfAccountsRepository;

    public function __construct(HRMSChartOfAccountsRepository $hRMSChartOfAccountsRepo)
    {
        $this->hRMSChartOfAccountsRepository = $hRMSChartOfAccountsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSChartOfAccounts",
     *      summary="Get a listing of the HRMSChartOfAccounts.",
     *      tags={"HRMSChartOfAccounts"},
     *      description="Get all HRMSChartOfAccounts",
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
     *                  @SWG\Items(ref="#/definitions/HRMSChartOfAccounts")
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
        $this->hRMSChartOfAccountsRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSChartOfAccountsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->all();

        return $this->sendResponse($hRMSChartOfAccounts->toArray(), trans('custom.h_r_m_s_chart_of_accounts_retrieved_successfully'));
    }

    /**
     * @param CreateHRMSChartOfAccountsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSChartOfAccounts",
     *      summary="Store a newly created HRMSChartOfAccounts in storage",
     *      tags={"HRMSChartOfAccounts"},
     *      description="Store HRMSChartOfAccounts",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSChartOfAccounts that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSChartOfAccounts")
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
     *                  ref="#/definitions/HRMSChartOfAccounts"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSChartOfAccountsAPIRequest $request)
    {
        $input = $request->all();

        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->create($input);

        return $this->sendResponse($hRMSChartOfAccounts->toArray(), trans('custom.h_r_m_s_chart_of_accounts_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSChartOfAccounts/{id}",
     *      summary="Display the specified HRMSChartOfAccounts",
     *      tags={"HRMSChartOfAccounts"},
     *      description="Get HRMSChartOfAccounts",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSChartOfAccounts",
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
     *                  ref="#/definitions/HRMSChartOfAccounts"
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
        /** @var HRMSChartOfAccounts $hRMSChartOfAccounts */
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            return $this->sendError(trans('custom.h_r_m_s_chart_of_accounts_not_found'));
        }

        return $this->sendResponse($hRMSChartOfAccounts->toArray(), trans('custom.h_r_m_s_chart_of_accounts_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSChartOfAccountsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSChartOfAccounts/{id}",
     *      summary="Update the specified HRMSChartOfAccounts in storage",
     *      tags={"HRMSChartOfAccounts"},
     *      description="Update HRMSChartOfAccounts",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSChartOfAccounts",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSChartOfAccounts that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSChartOfAccounts")
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
     *                  ref="#/definitions/HRMSChartOfAccounts"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSChartOfAccountsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSChartOfAccounts $hRMSChartOfAccounts */
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            return $this->sendError(trans('custom.h_r_m_s_chart_of_accounts_not_found'));
        }

        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->update($input, $id);

        return $this->sendResponse($hRMSChartOfAccounts->toArray(), trans('custom.hrmschartofaccounts_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSChartOfAccounts/{id}",
     *      summary="Remove the specified HRMSChartOfAccounts from storage",
     *      tags={"HRMSChartOfAccounts"},
     *      description="Delete HRMSChartOfAccounts",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSChartOfAccounts",
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
        /** @var HRMSChartOfAccounts $hRMSChartOfAccounts */
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            return $this->sendError(trans('custom.h_r_m_s_chart_of_accounts_not_found'));
        }

        $hRMSChartOfAccounts->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_chart_of_accounts_deleted_successfully'));
    }
}
