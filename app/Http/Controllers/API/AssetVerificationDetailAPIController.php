<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateAssetVerificationDetailAPIRequest;
use App\Http\Requests\API\UpdateAssetVerificationDetailAPIRequest;
use App\Models\AssetVerification;
use App\Models\AssetVerificationDetail;
use App\Repositories\AssetVerificationDetailRepository;
use Illuminate\Http\Request;
use Response;

/**
 * Class AssetVerificationDetailController
 *
 * @package App\Http\Controllers\API
 */
class AssetVerificationDetailAPIController extends AppBaseController
{
    /** @var  AssetVerificationDetailRepository */
    private $assetVerificationDetailRepository;

    public function __construct(AssetVerificationDetailRepository $assetVerificationDetailRepo)
    {
        $this->assetVerificationDetailRepository = $assetVerificationDetailRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetVerificationDetails",
     *      summary="Get a listing of the AssetVerificationDetails.",
     *      tags={"AssetVerificationDetail"},
     *      description="Get all AssetVerificationDetails",
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
     *                  @SWG\Items(ref="#/definitions/AssetVerificationDetail")
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
        $input = $request->all();
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

        $assetVerifications = AssetVerificationDetail::with(['assets:faID,faCode,assetDescription', 'assetVerification'])
            ->whereIN('companySystemID', $subCompanies)->where('verification_id', $input['verificationId']);

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

    /**
     * @param CreateAssetVerificationDetailAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetVerificationDetails",
     *      summary="Store a newly created AssetVerificationDetail in storage",
     *      tags={"AssetVerificationDetail"},
     *      description="Store AssetVerificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetVerificationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetVerificationDetail")
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
     *                  ref="#/definitions/AssetVerificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store($id, CreateAssetVerificationDetailAPIRequest $request)
    {
        $input = $request->all();

        $assetVerification = AssetVerification::find($input['verification_id']);

        $rows = [];

        $createdPcID = gethostname();
        $createdUserID = \Helper::getEmployeeID();
        $createdUserSystemID = \Helper::getEmployeeSystemID();

        $this->assetVerificationDetailRepository->deleteWhere(['verification_id' => $input['verification_id']]);

        $verificationArray = $assetVerification->toArray();
        foreach ($input['assets'] as $asset) {
            $row['faID'] = $asset;
            $row['verification_id'] = $input['verification_id'];
            $row['verifiedDate'] = $verificationArray['documentDate'];
            $row['createdPcID'] = $createdPcID;
            $row['createdUserID'] = $createdUserID;
            $row['createdUserSystemID'] = $createdUserSystemID;
            $row['companySystemID'] = $input['companySystemID'];
            array_push($rows, $row);
        }
        if (count($rows)) {
            $this->assetVerificationDetailRepository->insert($rows);
        }

        return $this->sendResponse([], trans('custom.asset_verification_detail_saved_successfully'));
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetVerificationDetails/{id}",
     *      summary="Display the specified AssetVerificationDetail",
     *      tags={"AssetVerificationDetail"},
     *      description="Get AssetVerificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetVerificationDetail",
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
     *                  ref="#/definitions/AssetVerificationDetail"
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
        /** @var AssetVerificationDetail $assetVerificationDetail */
        $assetVerificationDetail = $this->assetVerificationDetailRepository->findWithoutFail($id);

        if (empty($assetVerificationDetail)) {
            return $this->sendError(trans('custom.asset_verification_detail_not_found'));
        }

        return $this->sendResponse($assetVerificationDetail->toArray(), trans('custom.asset_verification_detail_retrieved_successfully'));
    }

    /**
     * @param int                                     $id
     * @param UpdateAssetVerificationDetailAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetVerificationDetails/{id}",
     *      summary="Update the specified AssetVerificationDetail in storage",
     *      tags={"AssetVerificationDetail"},
     *      description="Update AssetVerificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetVerificationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetVerificationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetVerificationDetail")
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
     *                  ref="#/definitions/AssetVerificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetVerificationDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetVerificationDetail $assetVerificationDetail */
        $assetVerificationDetail = $this->assetVerificationDetailRepository->findWithoutFail($id);

        if (empty($assetVerificationDetail)) {
            return $this->sendError(trans('custom.asset_verification_detail_not_found'));
        }

        $assetVerificationDetail = $this->assetVerificationDetailRepository->update($input, $id);

        return $this->sendResponse($assetVerificationDetail->toArray(), trans('custom.assetverificationdetail_updated_successfully'));
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetVerificationDetails/{id}",
     *      summary="Remove the specified AssetVerificationDetail from storage",
     *      tags={"AssetVerificationDetail"},
     *      description="Delete AssetVerificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetVerificationDetail",
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
    public function destroy($id, Request $request)
    {
        $input = $request->all();

        $assetVerification = AssetVerification::find($input['verification_id']);

        if ($assetVerification['approved']) {
            return $this->sendError(trans('custom.cannot_remove_asset'));
        }

        $assetVerificationDetail = $this->assetVerificationDetailRepository->findWithoutFail($id);

        if (empty($assetVerificationDetail)) {
            return $this->sendError(trans('custom.asset_verification_detail_not_found'));
        }

        $assetVerificationDetail->delete();

        return $this->sendResponse([],trans('custom.asset_verification_detail_deleted_successfully'));
    }

    public function listAllAsset()
    {

    }
}
