<?php
/**
 * =============================================
 * -- File Name : FixedAssetMasterReferredHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Fixed Asset Master Referback
 * -- REVISION HISTORY
 * -- Date: 26 - Novemeber 2018 By:Mubashir Description: Added new functions named as getAllAssetCostingAmendHistory(),assetCostingHistoryByAutoID()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetMasterReferredHistoryAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetMasterReferredHistoryAPIRequest;
use App\Models\FixedAssetMasterReferredHistory;
use App\Repositories\FixedAssetMasterReferredHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetMasterReferredHistoryController
 * @package App\Http\Controllers\API
 */

class FixedAssetMasterReferredHistoryAPIController extends AppBaseController
{
    /** @var  FixedAssetMasterReferredHistoryRepository */
    private $fixedAssetMasterReferredHistoryRepository;

    public function __construct(FixedAssetMasterReferredHistoryRepository $fixedAssetMasterReferredHistoryRepo)
    {
        $this->fixedAssetMasterReferredHistoryRepository = $fixedAssetMasterReferredHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasterReferredHistories",
     *      summary="Get a listing of the FixedAssetMasterReferredHistories.",
     *      tags={"FixedAssetMasterReferredHistory"},
     *      description="Get all FixedAssetMasterReferredHistories",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetMasterReferredHistory")
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
        $this->fixedAssetMasterReferredHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetMasterReferredHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetMasterReferredHistories = $this->fixedAssetMasterReferredHistoryRepository->all();

        return $this->sendResponse($fixedAssetMasterReferredHistories->toArray(), trans('custom.fixed_asset_master_referred_histories_retrieved_su'));
    }

    /**
     * @param CreateFixedAssetMasterReferredHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetMasterReferredHistories",
     *      summary="Store a newly created FixedAssetMasterReferredHistory in storage",
     *      tags={"FixedAssetMasterReferredHistory"},
     *      description="Store FixedAssetMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMasterReferredHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMasterReferredHistory")
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
     *                  ref="#/definitions/FixedAssetMasterReferredHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetMasterReferredHistoryAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetMasterReferredHistories = $this->fixedAssetMasterReferredHistoryRepository->create($input);

        return $this->sendResponse($fixedAssetMasterReferredHistories->toArray(), trans('custom.fixed_asset_master_referred_history_saved_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasterReferredHistories/{id}",
     *      summary="Display the specified FixedAssetMasterReferredHistory",
     *      tags={"FixedAssetMasterReferredHistory"},
     *      description="Get FixedAssetMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMasterReferredHistory",
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
     *                  ref="#/definitions/FixedAssetMasterReferredHistory"
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
        /** @var FixedAssetMasterReferredHistory $fixedAssetMasterReferredHistory */
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            return $this->sendError(trans('custom.fixed_asset_master_referred_history_not_found'));
        }

        return $this->sendResponse($fixedAssetMasterReferredHistory->toArray(), trans('custom.fixed_asset_master_referred_history_retrieved_succ'));
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetMasterReferredHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetMasterReferredHistories/{id}",
     *      summary="Update the specified FixedAssetMasterReferredHistory in storage",
     *      tags={"FixedAssetMasterReferredHistory"},
     *      description="Update FixedAssetMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMasterReferredHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMasterReferredHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMasterReferredHistory")
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
     *                  ref="#/definitions/FixedAssetMasterReferredHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetMasterReferredHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetMasterReferredHistory $fixedAssetMasterReferredHistory */
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            return $this->sendError(trans('custom.fixed_asset_master_referred_history_not_found'));
        }

        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->update($input, $id);

        return $this->sendResponse($fixedAssetMasterReferredHistory->toArray(), trans('custom.fixedassetmasterreferredhistory_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetMasterReferredHistories/{id}",
     *      summary="Remove the specified FixedAssetMasterReferredHistory from storage",
     *      tags={"FixedAssetMasterReferredHistory"},
     *      description="Delete FixedAssetMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMasterReferredHistory",
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
        /** @var FixedAssetMasterReferredHistory $fixedAssetMasterReferredHistory */
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            return $this->sendError(trans('custom.fixed_asset_master_referred_history_not_found'));
        }

        $fixedAssetMasterReferredHistory->delete();

        return $this->sendResponse($id, trans('custom.fixed_asset_master_referred_history_deleted_succes'));
    }


    public function getAllAssetCostingAmendHistory(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetCositng = FixedAssetMasterReferredHistory::with(['category_by', 'sub_category_by'])->where('faID',$input['faID']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%")
                    ->orWhere('COMMENTS', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faReferredID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }


    public function assetCostingHistoryByAutoID(Request $request){

        $fixedAssetMaster = FixedAssetMasterReferredHistory::with(['confirmed_by', 'group_to', 'posttogl_by'])->find($request->faID);

        if (empty($fixedAssetMaster)) {
            return $this->sendError(trans('custom.fixed_asset_master_not_found'));
        }

        return $this->sendResponse($fixedAssetMaster->toArray(), trans('custom.fixed_asset_master_retrieved_successfully'));
    }
}
