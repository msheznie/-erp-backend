<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPAssetVerificationDetailReferredbackAPIRequest;
use App\Http\Requests\API\UpdateERPAssetVerificationDetailReferredbackAPIRequest;
use App\Models\ERPAssetVerificationDetailReferredback;
use App\Repositories\ERPAssetVerificationDetailReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ERPAssetVerificationReferredback;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ERPAssetVerificationDetailReferredbackController
 * @package App\Http\Controllers\API
 */

class ERPAssetVerificationDetailReferredbackAPIController extends AppBaseController
{
    /** @var  ERPAssetVerificationDetailReferredbackRepository */
    private $eRPAssetVerificationDetailReferredbackRepository;

    public function __construct(ERPAssetVerificationDetailReferredbackRepository $eRPAssetVerificationDetailReferredbackRepo)
    {
        $this->eRPAssetVerificationDetailReferredbackRepository = $eRPAssetVerificationDetailReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetVerificationDetailReferredbacks",
     *      summary="Get a listing of the ERPAssetVerificationDetailReferredbacks.",
     *      tags={"ERPAssetVerificationDetailReferredback"},
     *      description="Get all ERPAssetVerificationDetailReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/ERPAssetVerificationDetailReferredback")
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
        $this->eRPAssetVerificationDetailReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->eRPAssetVerificationDetailReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eRPAssetVerificationDetailReferredbacks = $this->eRPAssetVerificationDetailReferredbackRepository->all();

        return $this->sendResponse($eRPAssetVerificationDetailReferredbacks->toArray(), trans('custom.e_r_p_asset_verification_detail_referredbacks_retr'));
    }

    /**
     * @param CreateERPAssetVerificationDetailReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eRPAssetVerificationDetailReferredbacks",
     *      summary="Store a newly created ERPAssetVerificationDetailReferredback in storage",
     *      tags={"ERPAssetVerificationDetailReferredback"},
     *      description="Store ERPAssetVerificationDetailReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetVerificationDetailReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetVerificationDetailReferredback")
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
     *                  ref="#/definitions/ERPAssetVerificationDetailReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateERPAssetVerificationDetailReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $eRPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepository->create($input);

        return $this->sendResponse($eRPAssetVerificationDetailReferredback->toArray(), trans('custom.e_r_p_asset_verification_detail_referredback_saved'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetVerificationDetailReferredbacks/{id}",
     *      summary="Display the specified ERPAssetVerificationDetailReferredback",
     *      tags={"ERPAssetVerificationDetailReferredback"},
     *      description="Get ERPAssetVerificationDetailReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetVerificationDetailReferredback",
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
     *                  ref="#/definitions/ERPAssetVerificationDetailReferredback"
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
        /** @var ERPAssetVerificationDetailReferredback $eRPAssetVerificationDetailReferredback */
        $eRPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepository->findWithoutFail($id);

        if (empty($eRPAssetVerificationDetailReferredback)) {
            return $this->sendError(trans('custom.e_r_p_asset_verification_detail_referredback_not_f'));
        }

        return $this->sendResponse($eRPAssetVerificationDetailReferredback->toArray(), trans('custom.e_r_p_asset_verification_detail_referredback_retri'));
    }

    /**
     * @param int $id
     * @param UpdateERPAssetVerificationDetailReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eRPAssetVerificationDetailReferredbacks/{id}",
     *      summary="Update the specified ERPAssetVerificationDetailReferredback in storage",
     *      tags={"ERPAssetVerificationDetailReferredback"},
     *      description="Update ERPAssetVerificationDetailReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetVerificationDetailReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetVerificationDetailReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetVerificationDetailReferredback")
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
     *                  ref="#/definitions/ERPAssetVerificationDetailReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateERPAssetVerificationDetailReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ERPAssetVerificationDetailReferredback $eRPAssetVerificationDetailReferredback */
        $eRPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepository->findWithoutFail($id);

        if (empty($eRPAssetVerificationDetailReferredback)) {
            return $this->sendError(trans('custom.e_r_p_asset_verification_detail_referredback_not_f'));
        }

        $eRPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepository->update($input, $id);

        return $this->sendResponse($eRPAssetVerificationDetailReferredback->toArray(), trans('custom.erpassetverificationdetailreferredback_updated_suc'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eRPAssetVerificationDetailReferredbacks/{id}",
     *      summary="Remove the specified ERPAssetVerificationDetailReferredback from storage",
     *      tags={"ERPAssetVerificationDetailReferredback"},
     *      description="Delete ERPAssetVerificationDetailReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetVerificationDetailReferredback",
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
        /** @var ERPAssetVerificationDetailReferredback $eRPAssetVerificationDetailReferredback */
        $eRPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepository->findWithoutFail($id);

        if (empty($eRPAssetVerificationDetailReferredback)) {
            return $this->sendError(trans('custom.e_r_p_asset_verification_detail_referredback_not_f'));
        }

        $eRPAssetVerificationDetailReferredback->delete();

        return $this->sendSuccess('E R P Asset Verification Detail Referredback deleted successfully');
    }

    public function fetchAssetVerificationDetailAmend(Request $request)
    {
        $input = $request->all(); 
        $assetVerification = ERPAssetVerificationReferredback::where('assetVerificationMasterRefferedBackID', $input['verificationId'])->first(); 

/*         $assetVerificationDetails = ERPAssetVerificationDetailReferredback::where('verification_id', $assetVerification->id)
        ->where('timesReferred', $assetVerification->timesReferred)->get();

 */

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetVerifications = ERPAssetVerificationDetailReferredback::with(['assets:faID,faCode,assetDescription', 'assetVerification'])
            ->whereIN('companySystemID', $subCompanies)
            ->where('verification_id',  $assetVerification->id)
            ->where('timesReferred',  $assetVerification->timesReferred);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetVerifications = $assetVerifications->whereHas('assets', function ($query) use ($search) {
                $query->where('assetDescription', 'LIKE', "%{$search}%")
                    ->orWhere('faCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetVerifications)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
