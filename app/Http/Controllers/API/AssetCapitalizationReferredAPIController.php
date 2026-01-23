<?php
/**
 * =============================================
 * -- File Name : AssetCapitalizationReferredAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Asset Capitalization Referback
 * -- REVISION HISTORY
 * -- Date: 26 - Novemeber 2018 By:Mubashir Description: Added new functions named as getAllCapitalizationAmendHistory(),assetCapitalizationHistoryByID()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizationReferredAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizationReferredAPIRequest;
use App\Models\AssetCapitalizationReferred;
use App\Repositories\AssetCapitalizationReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;

/**
 * Class AssetCapitalizationReferredController
 * @package App\Http\Controllers\API
 */

class AssetCapitalizationReferredAPIController extends AppBaseController
{
    /** @var  AssetCapitalizationReferredRepository */
    private $assetCapitalizationReferredRepository;

    public function __construct(AssetCapitalizationReferredRepository $assetCapitalizationReferredRepo)
    {
        $this->assetCapitalizationReferredRepository = $assetCapitalizationReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationReferreds",
     *      summary="Get a listing of the AssetCapitalizationReferreds.",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Get all AssetCapitalizationReferreds",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalizationReferred")
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
        $this->assetCapitalizationReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizationReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizationReferreds = $this->assetCapitalizationReferredRepository->all();

        return $this->sendResponse($assetCapitalizationReferreds->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalization_referred')]));
    }

    /**
     * @param CreateAssetCapitalizationReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizationReferreds",
     *      summary="Store a newly created AssetCapitalizationReferred in storage",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Store AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationReferred that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationReferred")
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
     *                  ref="#/definitions/AssetCapitalizationReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizationReferredAPIRequest $request)
    {
        $input = $request->all();

        $assetCapitalizationReferreds = $this->assetCapitalizationReferredRepository->create($input);

        return $this->sendResponse($assetCapitalizationReferreds->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_capitalization_referred')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationReferreds/{id}",
     *      summary="Display the specified AssetCapitalizationReferred",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Get AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationReferred",
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
     *                  ref="#/definitions/AssetCapitalizationReferred"
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
        /** @var AssetCapitalizationReferred $assetCapitalizationReferred */
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization_referred')]));
        }

        return $this->sendResponse($assetCapitalizationReferred->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalization_referred')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizationReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizationReferreds/{id}",
     *      summary="Update the specified AssetCapitalizationReferred in storage",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Update AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationReferred",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationReferred that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationReferred")
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
     *                  ref="#/definitions/AssetCapitalizationReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizationReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetCapitalizationReferred $assetCapitalizationReferred */
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization_referred')]));
        }

        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->update($input, $id);

        return $this->sendResponse($assetCapitalizationReferred->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_capitalization_referred')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetCapitalizationReferreds/{id}",
     *      summary="Remove the specified AssetCapitalizationReferred from storage",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Delete AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationReferred",
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
        /** @var AssetCapitalizationReferred $assetCapitalizationReferred */
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization_referred')]));
        }

        $assetCapitalizationReferred->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_capitalization_referred')]));
    }


    public function getAllCapitalizationAmendHistory(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetCositng = AssetCapitalizationReferred::with(['created_by'])->where('capitalizationID',$input['capitalizationID']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('capitalizationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('capitalizationReferredID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }


    public function assetCapitalizationHistoryByID(Request $request){

        $assetCapitalization = $this->assetCapitalizationReferredRepository->with(['confirmed_by', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'contra_account', 'asset_by' => function ($query) {
            $query->selectRaw("CONCAT(faCode,' - ',assetDescription) as assetName,faID");
        }])->findWithoutFail($request['capitalizationID']);

        if (empty($assetCapitalization)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization')]));
        }

        return $this->sendResponse($assetCapitalization->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalization')]));
    }
}
