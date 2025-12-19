<?php
/**
 * =============================================
 * -- File Name : AssetDisposalReferredAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Fixed Asset Master Referback
 * -- REVISION HISTORY
 * -- Date: 26 - Novemeber 2018 By:Mubashir Description: Added new functions named as getAllAssetDisposalAmendHistory(),assetDisposalHistoryByAutoID()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetDisposalReferredAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalReferredAPIRequest;
use App\Models\AssetDisposalReferred;
use App\Repositories\AssetDisposalReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDisposalReferredController
 * @package App\Http\Controllers\API
 */

class AssetDisposalReferredAPIController extends AppBaseController
{
    /** @var  AssetDisposalReferredRepository */
    private $assetDisposalReferredRepository;

    public function __construct(AssetDisposalReferredRepository $assetDisposalReferredRepo)
    {
        $this->assetDisposalReferredRepository = $assetDisposalReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalReferreds",
     *      summary="Get a listing of the AssetDisposalReferreds.",
     *      tags={"AssetDisposalReferred"},
     *      description="Get all AssetDisposalReferreds",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalReferred")
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
        $this->assetDisposalReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalReferreds = $this->assetDisposalReferredRepository->all();

        return $this->sendResponse($assetDisposalReferreds->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_referreds')]));
    }

    /**
     * @param CreateAssetDisposalReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalReferreds",
     *      summary="Store a newly created AssetDisposalReferred in storage",
     *      tags={"AssetDisposalReferred"},
     *      description="Store AssetDisposalReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalReferred that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalReferred")
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
     *                  ref="#/definitions/AssetDisposalReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalReferredAPIRequest $request)
    {
        $input = $request->all();

        $assetDisposalReferreds = $this->assetDisposalReferredRepository->create($input);

        return $this->sendResponse($assetDisposalReferreds->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_disposal_referreds')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalReferreds/{id}",
     *      summary="Display the specified AssetDisposalReferred",
     *      tags={"AssetDisposalReferred"},
     *      description="Get AssetDisposalReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalReferred",
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
     *                  ref="#/definitions/AssetDisposalReferred"
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
        /** @var AssetDisposalReferred $assetDisposalReferred */
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_referreds')]));
        }

        return $this->sendResponse($assetDisposalReferred->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_referreds')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalReferreds/{id}",
     *      summary="Update the specified AssetDisposalReferred in storage",
     *      tags={"AssetDisposalReferred"},
     *      description="Update AssetDisposalReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalReferred",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalReferred that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalReferred")
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
     *                  ref="#/definitions/AssetDisposalReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetDisposalReferred $assetDisposalReferred */
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_referreds')]));
        }

        $assetDisposalReferred = $this->assetDisposalReferredRepository->update($input, $id);

        return $this->sendResponse($assetDisposalReferred->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_disposal_referreds')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalReferreds/{id}",
     *      summary="Remove the specified AssetDisposalReferred from storage",
     *      tags={"AssetDisposalReferred"},
     *      description="Delete AssetDisposalReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalReferred",
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
        /** @var AssetDisposalReferred $assetDisposalReferred */
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_referreds')]));
        }

        $assetDisposalReferred->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_disposal_referreds')]));
    }

    public function getAllAssetDisposalAmendHistory(Request $request)
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

        $assetCositng = AssetDisposalReferred::with(['disposal_type', 'created_by'])->where('assetdisposalMasterAutoID',$input['assetdisposalMasterAutoID']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('disposalDocumentCode', 'LIKE', "%{$search}%");
                $query->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('assetdisposalMasterReferredID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }


    public function assetDisposalHistoryByAutoID(Request $request){

        $assetDisposalMaster = $this->assetDisposalReferredRepository->with(['financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'customer'])->findWithoutFail($request['assetdisposalMasterAutoID']);

        if (empty($assetDisposalMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_master')]));
        }

        return $this->sendResponse($assetDisposalMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_master')]));
    }
}
